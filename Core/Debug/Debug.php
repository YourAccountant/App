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
        $t = microtime(true);
        $micro = sprintf("%06d",($t - floor($t)) * 1000000);
        $d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );

        self::$data[strtolower($type)][strtolower($name)][$d->format("Y-m-d H:i:s.u")] = $params;
    }

    public static function get($type = null, $name = null)
    {
        if ($type == null) {
            return self::$data;
        } else if ($name == null) {
            return self::$data[$type];
        } else {
            return self::$data[$type][$name];
        }
    }
}
