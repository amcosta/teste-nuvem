<?php

namespace TiendaNube\Checkout\Http\Response;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class ResponseTest extends TestCase
{
    /**
     * @dataProvider providerForTestValidStatusCode
     * @param $code
     * @param $reasonPhrase
     */
    public function testValidStatusCode($code, $reasonPhrase)
    {
        $response = new Response();
        $result = $response->withStatus($code, $reasonPhrase);

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals($code, $result->getStatusCode());
        $this->assertEquals($reasonPhrase, $result->getReasonPhrase());
    }

    public function providerForTestValidStatusCode()
    {
        return [
            [200, 'Ok'],
            [404, 'Not Found'],
            [500, 'Internal Server Error']
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidStatusCode()
    {
        $response = new Response();
        $response->withStatus(null);
    }

    public function testGetBodyContent()
    {
        $address = [
            "altitude" => 7.0,
            "cep" => "40010000",
            "latitude" => "-12.967192",
            "longitude" => "-38.5101976",
            "address" => "Avenida da França",
            "neighborhood" => "Comércio",
            "city" => [
                "ddd" => 71,
                "ibge" => "2927408",
                "name" => "Salvador"
            ],
            "state" => [
                "acronym" => "BA"
            ]
        ];

        $body = json_encode($address);

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn($body);

        $response = new Response();
        $response->withBody($stream);

        $this->assertInstanceOf(StreamInterface::class, $response->getBody());
        $this->assertEquals($body, $response->getBody()->getContents());
    }

    public function testMethodWithHeaderWithValidArguments()
    {
        $response = new Response();

        $response->withHeader('content-type', 'application/json');
        $this->assertCount(1, $response->getHeaders());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));

        $values = ['value1', 'value2', 'value3', 'value4'];
        $response->withHeader('x-custom-header', $values);
        $this->assertCount(2, $response->getHeaders());
        $this->assertEquals($values, $response->getHeader('x-custom-header'));
        $this->assertEquals(implode(',', $values), $response->getHeaderLine('x-custom-header'));
    }

    public function testGetInvalidHeader()
    {
        $response = new Response();

        $invalidHeader = 'invalid-header';
        $this->assertCount(0, $response->getHeaders());
        $this->assertCount(0, $response->getHeader($invalidHeader));
        $this->assertEmpty($response->getHeaderLine($invalidHeader));
    }

    /**
     * @dataProvider providerForTestExceptionForSetInvalidHeader
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionForSetInvalidHeader($header, $value)
    {
        $response = new Response();
        $response->withHeader($header, $value);
    }

    public function providerForTestExceptionForSetInvalidHeader()
    {
        return [
            ['Content-Type', null],
            [null, 'text/html'],
            [null, null],
            ['', 'text/plain'],
            ['X-Custom', []],
            ['X-Custom', '']
        ];
    }
}