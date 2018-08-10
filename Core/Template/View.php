<?php

namespace Core\Template;

class View
{

    public static $base;

    public static $content;

    public static $data;

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
        include self::$base . '/' . trim($path, '/');
        $content = \ob_get_contents();
        \ob_end_clean();
        return $content;
    }

    public static function serve($view, $layout = null, $return = false)
    {
        $content = "";
        $viewContent = self::get($view);

        if ($layout != null) {
            self::$content = $viewContent;
            $content = self::get($layout);
        } else {
            $content = $viewContent;
        }

        if ($return) {
            return $content;
        }

        echo $content;
    }

}
