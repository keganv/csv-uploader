<?php

namespace Controller;

use Slim\Http\Response;

class PeopleController extends DefaultController
{
    /**
     * Stubs for People Controller
    public function index() {
        $data = array_merge($this->data, [
            'layout' => 'person/index',
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
            'layout' => 'person/view',
            'page_title' => 'Viewing: ' . $user->getFirstname(),
            'user' => $user,
        ]);

        return $this->container->get('renderer')->render($this->response, 'base.phtml', $data);
    }
    */
}
