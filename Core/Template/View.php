<?php

namespace Core\Template;

class View
{

    public static $base;

    public static $content;

    public static $data;

    public static $view;

    public static function setBase($path)
    {
        self::$base = rtrim(trim($path), '/');
    }

    public static function setData($arr)
    {
        self::$data = new \StdClass();
        foreach ($arr as $key => $value) {
            self::$data->{$key} = $value;
        }
        return self::$data;
    }

    private static function get($path)
    {
        $data = self::$data;
        \ob_start();
        self::include($path);
        $content = \ob_get_contents();
        \ob_end_clean();
        return $content;
    }

    public static function getViewContent()
    {
        $data = self::$data;
        if (View::$view != null)
        {
            include View::$view;
        }
    }

    public static function include($path, $get = false)
    {
        $path = self::$base . '/' . trim($path, '/');

        if (!$get) {
            include $path;
        }

        return $path;
    }

    public static function serve($view, $layout = null, $return = false)
    {
        if ($layout != null) {
            self::$view = self::include($view, true);
            $content = self::get($layout);
        } else {
            $content = self::get($view);
        }

        if ($return) {
            return $content;
        }

        echo $content;
    }

}
