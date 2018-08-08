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

    public function compile($view, $data = [])
    {
        $view = new View(self::getContent($view));
        $layout = trim($view->getLayout());

        if ($layout != null) {
            $layoutContent = (new View(self::getContent($layout)))->compile($data);
            $layout = new Layout($layoutContent);
            $this->content = $layout->compile($view, $data);
        } else {
            $this->content = $view->compile($data);
        }

        return $this->content;
    }
}
