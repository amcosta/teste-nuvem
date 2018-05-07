<?php

namespace TiendaNube\Checkout\Http\Router;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use TiendaNube\Checkout\Http\Request\RequestStackInterface;
use TiendaNube\Checkout\Http\Response\ResponseBuilderInterface;

class Router
{
    /**
     * @var array
     */
    private $routes = [];

    /**
     * @var RouterMatch
     */
    private $routerMatch;

    /**
     * @var RequestStackInterface
     */
    private $requestStack;

    /**
     * @var ResponseBuilderInterface
     */
    private $responseBuilder;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Router constructor.
     * @param RouterMatch $routerMatch
     * @param RequestStackInterface $requestStack
     * @param ResponseBuilderInterface $responseBuilder
     * @param ContainerInterface $container
     */
    public function __construct(
        RouterMatch $routerMatch,
        RequestStackInterface $requestStack,
        ResponseBuilderInterface $responseBuilder,
        ContainerInterface $container
    )
    {
        $this->container = $container;
        $this->routerMatch = $routerMatch;
        $this->requestStack = $requestStack;
        $this->responseBuilder = $responseBuilder;
    }

    /**
     * @param string $method
     * @param string $route
     * @param string $controller
     * @param array $regex
     */
    public function addRoute(string $method, string $route, string $controller, array $regex = [])
    {
        $data = [
            'route' => $route,
            'regex' => $regex,
            'method' => $method,
            'controller' => $controller
        ];

        array_push($this->routes, $data);
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @return ResponseInterface
     */
    public function run(): ResponseInterface
    {
        foreach ($this->routes as $routeItem) {
            $uri = $this->requestStack->getCurrentRequest()->getUri();
            $route = $routeItem['route'];
            $regex = $routeItem['regex'];
            $controllerString = $routeItem['controller'];

            if ($this->routerMatch->verify($uri, $route, $regex)) {
                $params = $this->routerMatch->getParams();

                return $this->executeController($controllerString, $params);
            }

            return $this->responseBuilder->buildResponse(null, 404, ['Contant-Type' => 'application/json']);
        }
    }

    /**
     * @param string $controllerString
     * @param array $params
     * @return mixed
     */
    private function executeController(string $controllerString, array $params = [])
    {
        list($controllerClass, $controllerMethod) = explode(':', $controllerString);

        $factory = $this->container->get('controller.factory');

        try {
            $controller = $factory->create($controllerClass);
        } catch (\RuntimeException $e) {
            return $this->responseBuilder->buildResponse(null, 500, ['Contant-Type' => 'application/json']);
        }

        return $controller->$controllerMethod(extract($params));
    }
}