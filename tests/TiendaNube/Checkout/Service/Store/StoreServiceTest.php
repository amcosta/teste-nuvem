<?php

namespace TiendaNube\Checkout\Service\Store;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use TiendaNube\Checkout\Model\Store;

class StoreServiceTest extends TestCase
{
    public function testVerifyInterface()
    {
        $service = $service = $this->buildStoreService();
        $this->assertInstanceOf(StoreServiceInterface::class, $service);
    }

    public function testRetriveStoreFromService()
    {
        $logger = $this->mockLogger();
        $request = $this->mockRequest('YouShallPass');

        $service = new StoreService($request, $logger);

        $this->assertInstanceOf(Store::class, $service->getCurrentStore());
    }

    /**
     * @dataProvider providerForTestStoreNotFoundInvalidToken
     * @expectedException \InvalidArgumentException
     */
    public function testStoreNotFoundInvalidToken($token)
    {
        $logger = $this->mockLogger();
        $request = $this->mockRequest($token);

        $service = new StoreService($request, $logger);
        $service->getCurrentStore();
    }

    public function providerForTestStoreNotFoundInvalidToken()
    {
        return [
            [''],
            ['YouShallNotPass']
        ];
    }

    private function buildStoreService()
    {
        $logger = $this->mockLogger();
        $request = $this->mockRequest();

        return new StoreService($request, $logger);
    }

    private function mockLogger()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->method('info')->withAnyParameters()->willReturn(null);
        $logger->method('alert')->withAnyParameters()->willReturn(null);
        $logger->method('critical')->withAnyParameters()->willReturn(null);

        return $logger;
    }

    private function mockRequest($headerAuthorizationBearerValue = null)
    {
        $request = $this->createMock(RequestInterface::class);
        $request->method('getHeaderLine')->with('Authentication-Bearer')->willReturn($headerAuthorizationBearerValue);

        return $request;
    }
}