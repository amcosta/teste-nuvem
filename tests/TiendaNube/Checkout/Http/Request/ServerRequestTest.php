<?php

namespace TiendaNube\Checkout\Http\Controller;

use PHPUnit\Framework\TestCase;
use TiendaNube\Checkout\Http\Request\ServerRequest;

class ServerRequestTest extends TestCase
{
    public function testGetAuthenticationBearer()
    {
        $value = 'YouShallPass';
        $_SERVER['HTTP_AUTHENTICATION_BEARER'] = $value;

        $serverRequest = new ServerRequest();

        $this->assertEquals($value, $serverRequest->getHeaderLine('Authentication-bearer'));
    }

    public function testGetContentType()
    {
        $value = 'application/json';
        $_SERVER['HTTP_CONTENT_TYPE'] = $value;

        $serverRequest = new ServerRequest();

        $this->assertEquals($value, $serverRequest->getHeaderLine('Content_type'));
    }

    public function testGetRequestMethod()
    {
        $method = 'GET';
        $_SERVER['REQUEST_METHOD'] = $method;

        $serverRequest = new ServerRequest();

        $this->assertEquals($method, $serverRequest->getMethod());
    }

    public function testNotExistsHeader()
    {
        $serverRequest = new ServerRequest();
        $this->assertNull($serverRequest->getHeaderLine('not-exist'));
    }
}