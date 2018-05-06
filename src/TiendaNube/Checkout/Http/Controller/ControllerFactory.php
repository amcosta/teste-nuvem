<?php

namespace TiendaNube\Checkout\Http\Controller;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use TiendaNube\Checkout\Http\Request\RequestStackInterface;
use TiendaNube\Checkout\Http\Response\ResponseBuilderInterface;

class ControllerFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RequestStackInterface
     */
    private $requestStack;

    /**
     * @var ResponseBuilderInterface
     */
    private $responseBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ControllerFactory constructor.
     * @param ContainerInterface $container
     * @param RequestStackInterface $requestStack
     * @param ResponseBuilderInterface $responseBuilder
     */
    public function __construct(
        ContainerInterface $container,
        RequestStackInterface $requestStack,
        ResponseBuilderInterface $responseBuilder
    )
    {
        $this->container = $container;
        $this->requestStack = $requestStack;
        $this->responseBuilder = $responseBuilder;
        $this->logger = $container->get('logger');
    }

    /**
     * @param string $className
     * @throws \RuntimeException
     * @return mixed
     */
    public function create(string $className)
    {
        $controllerClass = 'TiendaNube\\Checkout\\Http\Controller\\' . $className;

        if (!class_exists($controllerClass)) {
            $message = sprintf('Class "%s" not found in the process of creating controller', $controllerClass);
            $this->logger->error($message);
            throw new \RuntimeException($message);
        }

        return new $controllerClass($this->container, $this->requestStack, $this->responseBuilder);
    }
}