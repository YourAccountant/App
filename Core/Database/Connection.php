<?php

namespace Core\Database;

use \PDO;
use \Core\Config\Config;

class Connection
{
    private static $instance;

    private static $connection;

    private static $config;

    private static $hooks = [];

    public static function boot(Config $config)
    {
        self::$config = $config;
        self::connect();
    }

    public function __construct()
    {
        if (self::$instance == null) {
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
            self::$connection = new PDO(
                "mysql:host=".self::$config->dbHost.";port=".self::$config->dbPort.";dbname=".self::$config->dbDatabase.";",
                self::$config->dbUser,
                self::$config->dbPass,
                [
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false
                ]
            );
        } catch (\PDOException $e) {
            self::hook("connect", false);
            die($e->getMessage());
            return false;
        }

        self::hook("connect", true);

        return true;
    }

    private static function hook($hook, ...$args)
    {
        if (!isset(self::$hooks[$hook])) {
            return;
        }

        foreach (self::$hooks[$hook] as $callback) {
            \call_user_func_array($callback, $args);
        }
    }

    public static function addHook($hook, $callback)
    {
        self::$hooks[$hook][] = $callback;
    }

    public static function getHooks()
    {
        return self::$hooks;
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
        self::hook("query", $sql, $prepares);
        try {
            $stmt = self::$connection->prepare($sql);
            $stmt->execute($prepares);
            return $stmt;
        } catch (\Exception $e) {
            print_r([$e->getMessage(),$sql, $prepares]);
        } catch (\Error $e) {
            print_r([$e->getMessage(), $sql, $prepares]);
        }
        die;
    }

    public function get()
    {
        return self::$connection;
    }
}
