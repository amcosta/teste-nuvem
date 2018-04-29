<?php

namespace TiendaNube\Checkout\Http\Response;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TiendaNube\Checkout\Http\Response\JsonBuilderResponse;

class JsonBuilderResponseTest extends TestCase
{
    public function testGetSuccessResponse()
    {
        $body = ['test' => 'test'];
        $status = 200;
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $mockStream = $this->createMock(StreamInterface::class);
        $mockStream->method('getContents')->willReturn(json_encode($body));
        $mockStream->method('getSize')->willReturn(strlen(json_encode($body)));

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('withAddedHeader')->withAnyParameters();
        $mockResponse->method('withBody')->withAnyParameters();
        $mockResponse->method('getStatusCode')->willReturn(200);
        $mockResponse->method('getHeaderLine')->with('Content-Type')->willReturn('application/json');
        $mockResponse->method('getBody')->willReturn($mockStream);

        $jsonBuilderResponse = new JsonBuilderResponse($mockResponse, $mockStream);
        $response = $jsonBuilderResponse->buildResponse($body, $status, $headers);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals($headers['Content-Type'], $response->getHeaderLine('Content-Type'));
        $this->assertEquals(json_encode($body), $response->getBody()->getContents());
        $this->assertEquals(strlen(json_encode($body)), $response->getBody()->getSize());
    }
}