<?php

namespace Service;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;

use League\Csv\CharsetConverter;
use League\Csv\Reader;

use \PDO;

/**
 * Class CsvUploader
 * @package Service
 */
class CsvUploader
{
    /** @var EntityManager */
    protected $em;

    /** @var string */
    const PERSON_TABLE = 'person';

    /** @var string */
    const PEOPLE_GROUP_TABLE = 'people_group';

    /**
     * CsvUploader constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $table
     * @return string JSON
     */
    public function uploadFile(string $table)
    {
        $response['status'] = 'error';
        if (!empty($_FILES)) {
            $fileName   = time().'_'.basename($_FILES["file"]["name"]); // Create a unique file name
            $targetDir  = __DIR__ . "/../../temp/"; // Create the upload path
            $targetFile = $targetDir . $fileName;
            $fileExt    = pathinfo($targetFile, PATHINFO_EXTENSION);
            $allowTypes = ['csv']; // Only allow csv file formats

            if (in_array($fileExt, $allowTypes)) {
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                    if ($response = $this->import($table, $targetFile)) {
                        return json_encode($response);
                    }
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'An error occurred. Please check your CSV structure.';
                }
            } else {
                $response['status'] = 'ext_error';
            }
        }

        //render response data in JSON format
        return json_encode($response);
    }

    /**
     * @param  string $table
     * @param  string $filePath
     * @return array
     */
    public function import(string $table, string $filePath) : array
    {
        // Setting the header offset to index all records with the header record and remove it from the iteration.
        $csv  = Reader::createFromPath($filePath)->setHeaderOffset(0);
        $conn = $this->em->getConnection();

        // Encode the CSV
        $input_bom = $csv->getInputBOM();

        if ($input_bom === Reader::BOM_UTF16_LE || $input_bom === Reader::BOM_UTF16_BE) {
            CharsetConverter::addTo($csv, 'utf-16', 'utf-8');
        }

        // Set the delimeter in the header
        $csv->setDelimiter(',');

        if ($table === $this::PEOPLE_GROUP_TABLE) {
            return $this->createGroup($csv, $conn);
        }

        return $this->createPeople($csv, $conn);
    }

    protected function createGroup(Reader $csv, Connection $conn) : array
    {
        $response['status'] = 'ok';
        $validKeys = ['group_id', 'group_name'];
        $keys = str_replace(' ', '', $csv->getHeader());

        if ($keys !== $validKeys) {
            $response['status'] = 'error';
            $response['message'] = "The CSV type does not match. Please check your file structure.";
            return $response;
        }

        $count = 0;
        foreach ($csv as $record) {
            $record = array_combine($keys, array_values($record));
            $stmt = $conn->prepare(
                "INSERT INTO `people_group` (id, name) " .
                "VALUES (:id, :groupName) " .
                "ON DUPLICATE KEY UPDATE `name` = :groupName"
            );
            // Validate the data before inserting it into the database
            $stmt->bindValue(':id', $record['group_id'], PDO::PARAM_INT);
            $stmt->bindValue(':groupName', $record['group_name'], PDO::PARAM_STR);
            $stmt->execute();
            $count++;
        }

        $response['message'] = $count === 1 ? "$count Group was updated!" : "$count Groups were updated!";

        return $response;
    }

    protected function createPeople(Reader $csv, Connection $conn) : array
    {
        $response['status'] = 'ok';
        $validKeys = ['person_id', 'first_name', 'last_name', 'email_address', 'group_id', 'state'];
        $keys = str_replace(' ', '', $csv->getHeader());

        if ($keys !== $validKeys) {
            $response['status'] = 'error';
            $response['message'] = "The CSV type does not match. Please check your file structure.";
            return $response;
        }

        $count = 0;
        foreach ($csv as $record) {
            $record = array_combine($keys, array_values($record));
            $stmt   = $conn->prepare(
                "INSERT INTO `person` (id, first_name, last_name, email_address, group_id, state) " .
                "VALUES (:id, :firstName, :lastName, :emailAddress, :groupId, :state) " .
                "ON DUPLICATE KEY UPDATE first_name = :firstName, last_name = :lastName, " .
                "email_address = :emailAddress, group_id = :groupId, state = :state"
            );
            // Validate the data before inserting it into the database
            $stmt->bindValue(':id', $record['person_id'], PDO::PARAM_INT);
            $stmt->bindValue(':firstName', $record['first_name'], PDO::PARAM_STR);
            $stmt->bindValue(':lastName', $record['last_name'], PDO::PARAM_STR);
            $stmt->bindValue(':emailAddress', $record['email_address'], PDO::PARAM_STR);
            $stmt->bindValue(':groupId', $record['group_id'], PDO::PARAM_INT);
            $stmt->bindValue(':state', $record['state'], PDO::PARAM_STR);
            $stmt->execute();
            $count ++;
        }

        $response['message'] = $count === 1 ? "$count Person was updated!" : "$count People were updated!";

        return $response;
    }
}