<?php

namespace TiendaNube\Checkout\Http\Response;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ResponseBuilder implements ResponseBuilderInterface
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var StreamInterface
     */
    private $stream;

    public function __construct(ResponseInterface $response, StreamInterface $stream)
    {
        $this->response = $response;
        $this->stream = $stream;
    }

    /**
     * Factory a Response object
     *
     * @param mixed $body
     * @param int $status
     * @param array $headers
     * @return ResponseInterface
     */
    public function buildResponse($body, int $status = 200, array $headers = []): ResponseInterface
    {
        $this->stream->write($body);

        $response = $this->response;

        foreach ($headers as $header => $value) {
            $response->withHeader($header, $value);
        }

        $response->withBody($this->stream);
        $response->withStatus($status);

        return $response;
    }
}