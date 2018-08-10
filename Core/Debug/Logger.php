<?php

namespace Core\Debug;

class Logger
{

    public static function init()
    {
        set_error_handler(function ($errno, ...$errStr) {
            self::catchErrors($errno, $errStr);
        });
    }

    public static function catchErrors($errno, $args)
    {
        print_r([$errno, $args]);
        die;
    }

    public static function generateDebugFooter()
    {
        $style = "";
        $bar = "";
    }
}
