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
     * @param  string $table
     * @param  string $filePath
     * @return string $msg
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
        $count = 0;

        foreach ($csv as $record) {
            // Check if the type matches the CSV data
            if (!isset($record['group_id'])) {
                $response['status'] = 'error';
                $response['message'] = "The specified CSV type does not match the data.";
                return $response;
            }

            $record = $this->cleanKeys($record);
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
        $count = 0;

        foreach ($csv as $record) {
            // Check if the type matches the CSV data
            if (!isset($record['person_id'])) {
                $response['status'] = 'error';
                $response['message'] = "The specified CSV type does not match the data.";
                return $response;
            }

            $keys   = str_replace(' ', '', array_keys($record));
            $record = array_combine($keys, array_values($record));
            $stmt = $conn->prepare(
                "INSERT INTO `person` (id, first_name, last_name, email_address, group_id, state) " .
                "VALUES (:id, :firstName, :lastName, :emailAddress, :groupId, :state) " .
                "ON DUPLICATE KEY UPDATE first_name = :firstName, last_name = :lastName, email_address = :emailAddress, group_id = :groupId, state = :state"
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

    /**
     * Removes white space from keys
     *
     * @param array $record
     * @return array
     */
    protected function cleanKeys(array $record)
    {
        $keys = str_replace(' ', '', array_keys($record));
        return array_combine($keys, array_values($record));
    }
}