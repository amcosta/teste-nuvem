<?php

namespace TiendaNube\Checkout\Service\Container;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use TiendaNube\Checkout\Http\Request\ServerRequest;
use TiendaNube\Checkout\Service\Container\ContainerException;

class ContainerTest extends TestCase
{
    public function testVerifyInterface()
    {
        $container = new Container();

        $this->assertInstanceOf(ContainerInterface::class, $container);
    }

    public function testInsertAndRetriveValuesInContainer()
    {
        $container = new Container();

        $container->set('string', 'test1');
        $container->set('array', [1, 2]);
        $container->set('float', 1.23);
        $container->set('object', new ServerRequest());

        $this->assertEquals('test1', $container->get('string'));

        $this->assertCount(2, $container->get('array'));
        $this->assertEquals([1, 2], $container->get('array'));

        $this->assertEquals(1.23, $container->get('float'));

        $this->assertInstanceOf(ServerRequestInterface::class, $container->get('object'));
    }

    public function testExceptionForDuplicateEntry()
    {
        $container = new Container();

        $this->expectException(ContainerException::class);

        $container->set('string', 'test1');
        $container->set('string', 'test2');
    }

    public function testExceptionForNotFoundEntry()
    {
        $container = new Container();

        $this->expectException(NotFoundException::class);

        $container->get('invalid-entry');
    }
}