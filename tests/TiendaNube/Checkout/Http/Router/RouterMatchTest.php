<?php

namespace TiendaNube\Checkout\Http\Router;

use PHPUnit\Framework\TestCase;

class RouterMatchTest extends TestCase
{
    public function testVerifyValidRoute()
    {
        $uri = '/address/40010000';
        $route = '/address/{zipcode}';
        $regex = ['zipcode' => '\d+'];

        $routerMatch = new RouterMatch();
        $actual = $routerMatch->verify($uri, $route, $regex);

        $this->assertTrue($actual);
        $this->assertTrue(is_array($routerMatch->getParams()));

        $params = $routerMatch->getParams();
        $this->assertCount(3, $params);
        $this->assertEquals('/address/40010000', $params[0]);
        $this->assertEquals('address', $params[1]);
        $this->assertEquals('40010000', $params[2]);

        $regex = ['zipcode' => '\d{8}'];
        $actual = $routerMatch->verify($uri, $route, $regex);

        $this->assertTrue($actual);
        $this->assertTrue(is_array($routerMatch->getParams()));

        $params = $routerMatch->getParams();
        $this->assertCount(3, $params);
        $this->assertEquals('/address/40010000', $params[0]);
        $this->assertEquals('address', $params[1]);
        $this->assertEquals('40010000', $params[2]);
    }

    public function testVerifyInvalidRoute()
    {
        $uri = '/address/4001000';
        $route = '/address/{zipcode}';
        $regex = ['zipcode' => '\d{8}'];

        $routerMatch = new RouterMatch();
        $actual = $routerMatch->verify($uri, $route, $regex);

        $this->assertFalse($actual);
    }

    public function testMountPattern()
    {
        $route = '/address/{zipcode}';
        $regex = ['zipcode' => '\d+'];

        $routerMatch = new RouterMatch();
        $pattern = $routerMatch->mountPattern($route, $regex);

        $this->assertEquals('/^\/(address)\/(\d+)$/', $pattern);

        $route = '/address/{zipcode}';
        $regex = ['zipcode' => '\d{8}'];

        $pattern = $routerMatch->mountPattern($route, $regex);

        $this->assertEquals('/^\/(address)\/(\d{8})$/', $pattern);
    }
}