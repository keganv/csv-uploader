<?php

namespace Service;

use Controller\DefaultController;

use Psr\Container\ContainerInterface;

use Slim\Http\Request;
use Slim\Http\Response;

class RouteGenerator
{
    /** @var ContainerInterface */
    protected static $container;

    /** @var Request */
    protected $request;

    /** @var Response */
    protected $response;

    /** @var array */
    protected $data = [];

    /**
     * DefaultController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        self::$container = $container;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $data
     * @return mixed
     */
    public static function buildRoute(Request $request, Response $response, array $data = [])
    {
        $class    = DefaultController::class;
        $action   = 'index';
        $entityId = null;

        if (isset($data['path'])) {
            $class    = self::getControllerClass($data);
            $action   = self::getAction($data);
            $entityId = self::getEntityId($data);
        }

        $controller = class_exists($class) ? new $class(self::$container, $request, $response, $data) : null;

        if (!$controller || !method_exists($controller, $action)) {
            return self::$container['renderer']->render($response->withStatus(404), '404.phtml', [
                'page_title' => '404 Not Found'
            ]);
        }

        return $controller->$action($entityId);
    }

    private static function getControllerClass(array $data) : string
    {
        $path  = ucfirst(explode('/', $data['path'])[0]);
        $class = 'Controller\\' . $path . 'Controller';
        if (class_exists($class)) {
            return $class;
        }

        return '';
    }

    private static function getAction(array $data) : string
    {
        return explode('/', $data['path'])[1] ?? 'index';
    }

    private static function getEntityId(array $data)
    {
        return explode('/', $data['path'])[2] ?? null;
    }
}
