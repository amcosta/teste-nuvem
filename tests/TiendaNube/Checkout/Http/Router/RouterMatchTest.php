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
        $params = $routerMatch->getParams();

        $this->assertTrue($actual);
        $this->assertTrue(is_array($params));
        $this->assertCount(1, $params);
        $this->assertEquals('40010000', $params[0]);

        $uri = '/resource/1';
        $route = '/{resource}/{id}';
        $regex = ['id' => '\d+'];

        $actual = $routerMatch->verify($uri, $route, $regex);
        $params = $routerMatch->getParams();

        $this->assertTrue($actual);
        $this->assertTrue(is_array($params));
        $this->assertCount(2, $params);
        $this->assertEquals('resource', $params[0]);
        $this->assertEquals('1', $params[1]);
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

        $this->assertEquals('/^\/address\/(\d+)$/', $pattern);

        $route = '/address/{zipcode}';
        $regex = ['zipcode' => '\d{8}'];

        $pattern = $routerMatch->mountPattern($route, $regex);

        $this->assertEquals('/^\/address\/(\d{8})$/', $pattern);
    }
}