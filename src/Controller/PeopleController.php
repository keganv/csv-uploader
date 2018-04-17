<?php

namespace Controller;

use Psr\Container\ContainerInterface;

use Slim\Http\Request;
use Slim\Http\Response;

class PeopleController
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
        $data = array_merge($this->data, [
            'layout' => 'index',
            'page_title' => 'People',
            'users' => $this->container->get('entity_manager')->getRepository('Entity\Person')->findAll(),
        ]);

        return $this->container->get('renderer')->render($this->response, 'base.phtml', $data);
    }

    public function view($entityId = null) {
        $user = $this->container->get('entity_manager')->getRepository('Entity\Person')->findOneBy(['id' => $entityId]);

        if (!$user) {
            return $this->container->get('renderer')->render($this->response->withStatus(404), '404.phtml');
        }

        $data = array_merge($this->data, [
            'layout' => 'view',
            'page_title' => 'Viewing: ' . $user->getFirstname(),
            'user' => $user,
        ]);

        return $this->container->get('renderer')->render($this->response, 'base.phtml', $data);
    }
}
