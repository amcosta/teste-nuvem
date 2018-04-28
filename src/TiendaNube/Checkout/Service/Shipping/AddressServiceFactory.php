<?php

namespace TiendaNube\Checkout\Service\Shipping;

use Psr\Log\LoggerInterface;
use TiendaNube\Checkout\Service\Store\StoreServiceInterface;

class AddressServiceFactory
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var StoreServiceInterface
     */
    private $storeService;

    public function __construct(\PDO $pdo, LoggerInterface $logger, StoreServiceInterface $storeService)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
        $this->storeService = $storeService;
    }

    public function create()
    {
        $store = $this->storeService->getCurrentStore();
        $serviceName = $store->isBetaTester() ? AddressServiceBeta::class : AddressService::class;

        if (!class_exists($serviceName)) {
            $this->logger->error(sprintf('Class "%s" not found in the process of creating the address service', $serviceName));
            throw new \RuntimeException();
        }

        return new $serviceName($this->pdo, $this->logger);
    }
}