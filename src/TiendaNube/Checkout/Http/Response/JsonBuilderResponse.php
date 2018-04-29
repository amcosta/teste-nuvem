<?php

namespace TiendaNube\Checkout\Http\Response;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class JsonBuilderResponse implements ResponseBuilderInterface
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

    public function buildResponse($body, int $status = 200, array $headers = []): ResponseInterface
    {
        $response = $this->response;

        $body = is_array($body) ?: json_encode($body);
        $this->stream->write($body);

        foreach ($headers as $header => $value) {
            $response->withHeader($header, $value);
        }

        $response->withAddedHeader('Content-Type', 'application/json');
        $response->withHeader('Content-Length', $this->stream->getSize());
        $response->withBody($this->stream);
        $response->withStatus($status);

        return $response;
    }
}