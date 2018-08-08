<?php

namespace Core\View;

class ViewProvider
{
    public static $path;

    public static $layout;

    public function __construct($path, $layout = null)
    {
        self::$path = $path;
        self::$layout = $layout;
    }

    public static function getContent($path)
    {
        return file_get_contents(rtrim(self::$path, '/') . '/' . trim($path, '/'));
    }

    public function compile($view)
    {
        $view = new View(self::getContent($view));
        $layout = trim($view->getLayout());

        if ($layout != null) {
            $layout = new Layout(self::getContent($layout));
            $this->content = $layout->compile($view);
        } else {
            $this->content = $view->compile();
        }

        return $this->content;
    }
}
