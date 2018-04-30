<?php

namespace TiendaNube\Checkout\Http\Request;

use Psr\Http\Message\ServerRequestInterface;

class HttpRequest implements RequestStackInterface
{
    /**
     * @var ServerRequestInterface
     */
    private $serverRequest;

    public function __construct(ServerRequestInterface $serverRequest)
    {
        $this->serverRequest = $serverRequest;
    }

    public function getCurrentRequest(): ServerRequestInterface
    {
        return $this->serverRequest;
    }
}