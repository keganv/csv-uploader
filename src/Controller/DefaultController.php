<?php

namespace Controller;

use Service\CsvUploader;

use Psr\Container\ContainerInterface;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class DefaultController
 * @package Controller
 */
class DefaultController
{
    /** @var ContainerInterface */
    protected $container;

    /** @var Request */
    protected $request;

    /** @var Response */
    protected $response;

    /** @var array */
    protected $data = [];

    /**
     * DefaultController constructor.
     * @param ContainerInterface $container
     * @param Request|null $request
     * @param Response|null $response
     * @param array $data
     */
    public function __construct(
        ContainerInterface $container,
        Request $request = null,
        Response $response = null,
        array $data = []
    ) {
        $this->container = $container;
        $this->request = $request;
        $this->response = $response;
        $this->data = $data;
    }

    /**
     * @return Response
     */
    public function index() {
        if ($this->request->getMethod() === 'POST') {
            return $this->container->get('renderer')->render($this->response->withStatus(405)->write('Not Allowed!'), '404.phtml');
        }

        $this->data['layout'] = 'index';
        return $this->container->get('renderer')->render($this->response, 'base.phtml', $this->data);
    }

    /**
     * @return string JSON
     */
    public function post() {
        if ($this->request->getMethod() !== 'POST') {
            return $this->container->get('renderer')->render($this->response->withStatus(405)->write('Not Allowed!'), '404.phtml');
        }

        $table    = $this->request->getParsedBodyParam('table');
        $uploader = $this->container->get('csv_uploader');

        if (!$this->hasGroups() && $table === 'person') {
            $response['status'] = 'error';
            $response['message'] = 'You must first import Groups. Please Check the type specified or the CSV data.';
            return json_encode($response);
        }

        return $uploader->uploadFile($table);
    }

    /**
     * @param int $groupId
     * @return array
     */
    public function peopledata($groupId = 0)
    {
        $this->data['groups'] = $this->container->get('people_data')->getPeopleData($groupId);
        return $this->container->get('renderer')->render($this->response, '/group/index.phtml', $this->data);
    }

    protected function hasGroups()
    {
        return $this->container
            ->get('entity_manager')
            ->getRepository('Entity\PeopleGroup')
            ->createQueryBuilder('g')
            ->select('COUNT(g.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}