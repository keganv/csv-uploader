<?php

namespace Tests\Functional;

use Doctrine\ORM\EntityManager;

use Service\CsvUploader;

class FileUploadTest extends BaseTestCase
{
    /** @var \Slim\App */
    protected $app;

    /** @var \Psr\Container\ContainerInterface */
    protected $container;

    /** @var EntityManager */
    protected $em;

    /** @var CsvUploader */
    protected $uploader;

    /**
     * FileUploadTest constructor.
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->app       = $this->createApp();
        $this->container = $this->app->getContainer();
        $this->em        = $this->container->get('entity_manager');
        $this->uploader  = new CsvUploader($this->em);
    }

    /**
     * Test uploading a clean people csv data file
     */
    public function testCleanPeopleCsvUpload()
    {
        $response = $this->uploader->import('person', __DIR__ . '/../../public/files/people.csv');
        $this->assertEquals('ok', $response['status']);
        $this->assertEquals('15 People were updated!', $response['message']);
    }

    /**
     * Test uploading a malformed people csv data file
     * The file has the wrong headings
     */
    public function testPeopleErrorCsvUpload()
    {
        $response = $this->uploader->import('person', __DIR__ . '/../../public/files/people_error.csv');
        $this->assertEquals('error', $response['status']);
        $this->assertEquals('The CSV type does not match. Please check your file structure.', $response['message']);
    }

    /**
     * Test uploading a clean group csv data file
     */
    public function testCleanGroupsCsvUpload()
    {
        $response = $this->uploader->import('people_group', __DIR__ . '/../../public/files/groups.csv');
        $this->assertEquals('ok', $response['status']);
        $this->assertEquals('4 Groups were updated!', $response['message']);
    }

    /**
     * Test uploading a malformed groups csv data file
     * The file has the wrong headings
     */
    public function testGroupsErrorCsvUpload()
    {
        $response = $this->uploader->import('people_group', __DIR__ . '/../../public/files/groups_error.csv');
        $this->assertEquals('error', $response['status']);
        $this->assertEquals('The CSV type does not match. Please check your file structure.', $response['message']);
    }

    protected function tearDown()
    {
        unset($this->app);
    }
}
