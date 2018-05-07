<?php

namespace TiendaNube\Checkout\Service\Database;

class Database
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var string
     */
    private $dabaseName = 'nuvem_shop';

    /**
     * @var string
     */
    private $host = 'mysqldb';

    /**
     * @var string
     */
    private $username = 'nuvem';

    /**
     * @var string
     */
    private $password = 'nuvem';

    /**
     * @var int string
     */
    private $port = 3306;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dabaseName};port={$this->port}";
        $this->pdo = new \PDO($dsn, $this->username, $this->password);
    }

    public function getConnection()
    {
        if (!$this->pdo instanceof \PDO) {
            $this->connect();
        }

        return $this->pdo;
    }
}