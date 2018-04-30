<?php

namespace TiendaNube\Checkout\Service\Shipping;

use Psr\Log\LoggerInterface;
use TiendaNube\Checkout\Service\Store\StoreServiceInterface;

/**
 * Class AddressServiceFactory
 * @package TiendaNube\Checkout\Service\Shipping
 */
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

    /**
     * AddressServiceFactory constructor.
     * @param \PDO $pdo
     * @param LoggerInterface $logger
     * @param StoreServiceInterface $storeService
     */
    public function __construct(\PDO $pdo, LoggerInterface $logger, StoreServiceInterface $storeService)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
        $this->storeService = $storeService;
    }

    /**
     * Return a AddressServiceBeta if the store is a beta tester otherwise return a AddressService
     *
     * @return AddressServiceInterface
     * @throws \RuntimeException
     */
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