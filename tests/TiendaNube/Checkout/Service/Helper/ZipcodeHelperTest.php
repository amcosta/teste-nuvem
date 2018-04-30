<?php

namespace TiendaNube\Checkout\Service\Helper;

use PHPUnit\Framework\TestCase;

class ZipcodeHelperTest extends TestCase
{
    /**
     * @dataProvider providerForTestSanitizeZipcode
     * @param $zipcode
     * @param $rawZipcode
     */
    public function testSanitizeZipcode($zipcode, $rawZipcode)
    {
        $result = ZipcodeHelper::sanitize($zipcode);
        $this->assertEquals($rawZipcode, $result);
    }

    public function providerForTestSanitizeZipcode()
    {
        return [
            ['40001000', '40001000'],
            ['4a0b0c0d1 0f0_0', '40001000']
        ];
    }
}