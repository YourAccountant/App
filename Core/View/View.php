<?php

namespace Core\View;

class View
{

    public $template;

    private $layoutPattern = "\@layout\((?:\'|\")(?<layout>.*)(?:\'|\")\)\;";

    public function __construct($template)
    {
        $this->template = $template;
    }

    public function getLayout()
    {
        preg_match("/{$this->layoutPattern}/", $this->template, $matches);
        return $matches['layout'] ?? null;
    }

    public function compile()
    {
        $pattern = "/\@include\((?:\'|\")(?<content>.*)(?:\'|\")\)\;/";
        $this->template = preg_replace("/{$this->layoutPattern}/", "", $this->template);

        if (preg_match($pattern, $this->template, $matches)) {
            $content = ViewProvider::getContent($matches['content']);
            $view = new View($content);
            $view->compile();
            $this->template = preg_replace($pattern, $view->template, $this->template);
        }

        return $this->template;
    }
}
