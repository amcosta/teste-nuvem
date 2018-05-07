<?php

require 'vendor/autoload.php';

$app = new \TiendaNube\Checkout\Service\Application\Application();

$app->get('/address/{zipcode}', 'CheckoutController:getAddressAction', ['zipcode' => '\d+']);

$app->run();