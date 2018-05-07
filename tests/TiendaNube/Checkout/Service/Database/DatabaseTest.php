<?php

namespace TiendaNube\Checkout\Service\Database;

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function testVerifyInstance()
    {
        $database = new Database();

        $this->assertInstanceOf(\PDO::class, $database->getConnection());
    }
}