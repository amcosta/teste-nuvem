<?php

namespace TiendaNube\Checkout\Http\Router;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use TiendaNube\Checkout\Http\Controller\CheckoutController;
use TiendaNube\Checkout\Http\Controller\ControllerFactory;
use TiendaNube\Checkout\Http\Request\Request;
use TiendaNube\Checkout\Http\Request\ServerRequest;
use TiendaNube\Checkout\Http\Response\Response;
use TiendaNube\Checkout\Http\Response\ResponseBuilder;
use TiendaNube\Checkout\Http\Response\Stream;

class RouteTest extends TestCase
{
    public function tearDown()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $_SERVER['REQUEST_URI'] = null;
    }

    public function testInsertRoutesToVerify()
    {
        $router = $this->buildRouter();
        $router->addRoute('GET', '/address/{zipcode}', 'CheckoutController:getAddress', ['zipcode' => '\d+']);
        $router->addRoute('GET', '/store', 'CheckoutController:getStore');

        $routes = $router->getRoutes();
        $this->assertCount(2, $routes);

        $this->assertEquals([
            'method' => 'GET',
            'route' => '/address/{zipcode}',
            'controller' => 'CheckoutController:getAddress',
            'regex' => ['zipcode' => '\d+']
        ], $routes[0]);

        $this->assertEquals([
            'method' => 'GET',
            'route' => '/store',
            'controller' => 'CheckoutController:getStore',
            'regex' => []
        ], $routes[1]);
    }

    public function testReturnResponseFromController()
    {
        $_SERVER['REQUEST_URI'] = '/address/40010000';

        $router = $this->buildRouter();
        $router->addRoute('GET', '/address/{zipcode}', 'CheckoutController:getAddressAction', ['zipcode' => '\d+']);

        $response = $router->run();
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testRouteNotFound()
    {
        $_SERVER['REQUEST_URI'] = '/invalid-route';

        $router = $this->buildRouter();
        $router->addRoute('GET', '/address/{zipcode}', 'CheckoutController:getAddressAction', ['zipcode' => '\d+']);

        $response = $router->run();
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
    }

    private function buildRouter()
    {
        $routerMatch = new RouterMatch();
        $requestStack = new Request(new ServerRequest());
        $responseBuilder = new ResponseBuilder(new Response(), new Stream());

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);

        $controller = $this->createMock(CheckoutController::class);
        $controller->method('getAddressAction')->withAnyParameters()->willReturn($response);

        $factory = $this->createMock(ControllerFactory::class);
        $factory->method('create')->with($this->equalTo('CheckoutController'))->willReturnCallback(function ($controllerString) use ($controller) {
            if ('CheckoutController' === $controllerString) {
                return $controller;
            }

            throw new \RuntimeException();
        });

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->with($this->equalTo('controller.factory'))->willReturn($factory);

        $router = new Router($routerMatch, $requestStack, $responseBuilder, $container);

        return $router;
    }
}