<?php

namespace TiendaNube\Checkout\Service\Helper;

/**
 * Class ZipcodeHelper
 * @package TiendaNube\Checkout\Service\Helper
 */
class ZipcodeHelper
{
    /**
     * @param $zipcode
     * @return null|string
     */
    static public function sanitize($zipcode)
    {
        return preg_replace("/[^\d]/", "", $zipcode);
    }
}