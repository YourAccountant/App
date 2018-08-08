<?php

namespace Core\Database;

use \PDO;
use \Core\Config\Config;

class Connection
{
    private static $instance;

    private static $connection;

    private static $config;

    public static function boot(Config $config)
    {
        self::$config = $config;
        self::connect();
    }

    public function __construct()
    {
        if (self::$instance == null)
        {
            self::$instance = $this;
        }
    }

    public static function hasConnection()
    {
        return self::$connection != null;
    }

    public static function connect()
    {
        try {
            self::$connection = new PDO("mysql:host=".self::$config->dbHost.";port=".self::$config->dbPort.";dbname=".self::$config->dbDatabase.";",
                self::$config->dbUser,
                self::$config->dbPass,
                [
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false
                ]
            );

            return true;
        } catch (\PDOException $e) {
            die($e->getMessage());
            return false;
        }
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    public static function builder($table)
    {
        return new QueryBuilder(self::getInstance(), $table);
    }

    public function query($sql, $prepares = [])
    {
        $stmt = self::$connection->prepare($sql);
        $stmt->execute($prepares);
        return $stmt;
    }

    public function get()
    {
        return self::$connection;
    }

}
