<?php

namespace Core\Database\Migration;

use \Core\Database\Connection;

class Migration
{
    private static $connection;

    private static $tables = [];

    public static function setConnection(Connection $connection)
    {
        self::$connection = $connection;
    }

    public static function table($name)
    {
        $table = new Table($name);
        self::$tables[$name] = $table;
        return $table;
    }

    public static function create()
    {
        foreach (self::$tables as $name => $table) {
            $sql = $table->create();
            if ($sql !=  null) {
                self::$connection->query($sql);
            }
        }
    }

    public static function sort($order)
    {
        self::$tables = array_merge(array_flip($order), self::$tables);
    }

    public static function get()
    {
        return self::$tables;
    }
}
