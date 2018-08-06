<?php

namespace Core\Database;

use \PDO;
use \Core\Config\Config;

class Connection
{

    private $connection;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->host = $this->config->dbHost;
        $this->port = $this->config->dbPort;
        $this->database = $this->config->dbDatabase;
        $this->user = $this->config->dbUser;
        $this->pass = $this->config->dbPass;
    }

    public function hasConnection()
    {
        return $this->connection != null;
    }

    public function connect()
    {
        try {

            $this->connection = new PDO("mysql:host={$this->host};port={$this->port};dbname={$this->database};",
                $this->user,
                $this->pass,
                [
                    PDO::ATTR_PERSISTENT => true
                ]
            );

            return true;
        } catch (\PDOException $e) {
            die($e->getMessage());
            return false;
        }
    }

    public function get()
    {
        return $this->connection;
    }

}
