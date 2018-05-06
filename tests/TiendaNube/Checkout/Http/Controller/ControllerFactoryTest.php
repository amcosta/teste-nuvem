<?php

namespace TiendaNube\Checkout\Http\Controller;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use TiendaNube\Checkout\Http\Request\RequestStackInterface;
use TiendaNube\Checkout\Http\Response\ResponseBuilder;

class ControllerFactoryTest extends TestCase
{
    public function testCreateController()
    {
        $requestStack = $this->createMock(RequestStackInterface::class);
        $responseBuilder = $this->createMock(ResponseBuilder::class);
        $container = $this->mockContainer();

        $controllerFactory = new ControllerFactory($container, $requestStack, $responseBuilder);
        $controller = $controllerFactory->create('CheckoutController');

        $this->assertInstanceOf(CheckoutController::class, $controller);
    }

    public function testControllerNotFound()
    {
        $requestStack = $this->createMock(RequestStackInterface::class);
        $responseBuilder = $this->createMock(ResponseBuilder::class);
        $container = $this->mockContainer();

        $controllerFactory = new ControllerFactory($container, $requestStack, $responseBuilder);

        $this->expectException(\RuntimeException::class);
        $controller = $controllerFactory->create('InvalidController');
    }

    private function mockContainer()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->method('error')->withAnyParameters()->willReturn(null);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->with('logger')->willReturn($logger);

        return $container;
    }
}