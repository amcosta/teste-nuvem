<?php

namespace TiendaNube\Checkout\Service\Application;

use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use TiendaNube\Checkout\Http\Controller\ControllerFactory;
use TiendaNube\Checkout\Http\Request\Request;
use TiendaNube\Checkout\Http\Request\RequestStackInterface;
use TiendaNube\Checkout\Http\Request\ServerRequest;
use TiendaNube\Checkout\Http\Response\Response;
use TiendaNube\Checkout\Http\Response\ResponseBuilder;
use TiendaNube\Checkout\Http\Response\ResponseBuilderInterface;
use TiendaNube\Checkout\Http\Response\Stream;
use TiendaNube\Checkout\Http\Router\Router;
use TiendaNube\Checkout\Http\Router\RouterMatch;
use TiendaNube\Checkout\Service\Container\Container;
use TiendaNube\Checkout\Service\Container\ContainerException;
use TiendaNube\Checkout\Service\Database\Database;
use TiendaNube\Checkout\Service\Logger\Logger;
use TiendaNube\Checkout\Service\Shipping\AddressServiceFactory;
use TiendaNube\Checkout\Service\Store\StoreService;

class Application
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RequestStackInterface
     */
    private $requestStack;

    /**
     * @var ResponseBuilderInterface
     */
    private $responseBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct()
    {
        $this->bootstrap();
    }

    private function bootstrap()
    {
        $routerMatch = new RouterMatch();
        $this->requestStack = new Request(new ServerRequest());
        $this->responseBuilder = new ResponseBuilder(new Response(), new Stream());
        $this->container = new Container();

        $this->logger = new Logger();
        $this->containerRegisterEntry('logger', $this->logger);

        $controllerFactory = new ControllerFactory($this->container, $this->requestStack, $this->responseBuilder);
        $this->containerRegisterEntry('controller.factory', $controllerFactory);

        $storeService = new StoreService($this->requestStack->getCurrentRequest(), $this->logger);
        $this->containerRegisterEntry('store.storeService', $storeService);

        $pdo = (new Database())->getConnection();
        $addressServiceFactory = new AddressServiceFactory($pdo, $this->logger, $storeService);
        $this->containerRegisterEntry('shipping.addressServiceFactory', $addressServiceFactory);

        $this->router = new Router($routerMatch, $this->requestStack, $this->responseBuilder, $this->container);
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function getRequestStack()
    {
        return $this->requestStack;
    }

    public function getResponseBuilder()
    {
        return $this->responseBuilder;
    }

    public function get(string $route, string $controller, array $regex = [])
    {
        $this->router->addRoute('GET', $route, $controller, $regex);
    }

    public function run()
    {
        if (!$this->verifyToken()) {
            return;
        }

        try {
            $response = $this->router->run();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $response = $this->responseBuilder->buildResponse(null, 500, ['Content-Type' => 'application/json']);
            $this->renderResponse($response);
        }

        $this->renderResponse($response);
    }

    private function renderResponse(ResponseInterface $response)
    {
        foreach ($response->getHeaders() as $header => $values) {
            $value = implode(',', $values);
            header("{$header}: {$value}");
        }

        http_response_code($response->getStatusCode());

        echo $response->getBody()->getContents();
    }

    private function containerRegisterEntry($id, $value)
    {
        try {
            $this->container->set($id, $value);
        } catch (ContainerException $e) {
            $this->logger->error($e->getMessage());
            $response = $this->responseBuilder->buildResponse(null, 500, ['Content-Type' => 'application/json']);
            $this->renderResponse($response);
        }
    }

    private function verifyToken()
    {
        try {
            $this->container->get('store.storeService')->getCurrentStore();
        } catch (\InvalidArgumentException $e) {
            $response = $this->responseBuilder->buildResponse(null, 401, ['Content-Type' => 'application/json']);
            $this->renderResponse($response);
            return false;
        }

        return true;
    }
}