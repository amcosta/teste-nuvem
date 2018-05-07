<?php

namespace TiendaNube\Checkout\Service\Application;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use TiendaNube\Checkout\Http\Request\RequestStackInterface;
use TiendaNube\Checkout\Http\Response\ResponseBuilderInterface;

class ApplicationTest extends TestCase
{
    public function testBootstrapOfApplication()
    {
        $app = new Application();

        $this->assertInstanceOf(ContainerInterface::class, $app->getContainer());
        $this->assertInstanceOf(RequestStackInterface::class, $app->getRequestStack());
        $this->assertInstanceOf(ResponseBuilderInterface::class, $app->getResponseBuilder());
    }
}