<?php

namespace Core\Database;

use \PDO;
use \Core\Config\Config;
use \Core\Debug\Debug;

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

            if (self::$config->isDev()) {
                die($e->getMessage());
            }

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
        $stmt = self::$connection->prepare($sql);

        try {
            if (method_exists($stmt, 'beginTransaction')) {
                $stmt->beginTransaction();
            }

            $stmt->execute($prepares);

            if (method_exists($stmt, 'commit')) {
                $stmt->commit();
            }

            return $stmt;
        } catch (\Exception $e) {
            if (method_exists($stmt, 'rollback')) {
                $stmt->rollback();
            }

            if (self::$config->isDev()) {
                Debug::print();
                die;
            }
        } catch (\Error $e) {
            if (method_exists($stmt, 'rollback')) {
                $stmt->rollback();
            }

            if (self::$config->isDev()) {
                Debug::print();
                die;
            }
        }
    }

    public function get()
    {
        return self::$connection;
    }
}
