<?php

namespace Core\Debug;

use \DateTime;

class Debug
{

    private $app;

    private $path;

    public static $data;

    private $database;

    private $logger;

    public function __construct($path)
    {
        $this->path = $path;
        $this->database = new DatabaseDebugger();
    }

    public static function add($type, $name, $params = [])
    {
        self::$data[strtolower($type)][strtolower($name)][] = $params;
    }

    public static function get($type = null, $name = null)
    {
        if ($type == null) {
            return self::$data;
        } elseif ($name == null) {
            return self::$data[$type];
        } else {
            return self::$data[$type][$name];
        }
    }

    public static function print($type = null, $name = null)
    {
        $d = self::get($type, $name);
        print_r($d);
        die;
    }
}
