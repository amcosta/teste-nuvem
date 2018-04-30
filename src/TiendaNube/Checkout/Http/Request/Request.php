<?php

namespace TiendaNube\Checkout\Http\Request;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Request
 * @package TiendaNube\Checkout\Http\Request
 */
class Request implements RequestStackInterface
{
    /**
     * @var ServerRequestInterface
     */
    private $serverRequest;

    /**
     * Request constructor.
     * @param ServerRequestInterface $serverRequest
     */
    public function __construct(ServerRequestInterface $serverRequest)
    {
        $this->serverRequest = $serverRequest;
    }

    /**
     * Get the current request object
     *
     * @return ServerRequestInterface
     */
    public function getCurrentRequest(): ServerRequestInterface
    {
        return $this->serverRequest;
    }
}