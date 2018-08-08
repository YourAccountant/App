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

    public function replaceData($data = [])
    {
        $pattern = "\{\{\s*(?<var>.*)\s*\}\}";
        if (preg_match("/$pattern/", $this->template, $matches)) {
            $var = trim($matches['var']);
            if (isset($data[$var])) {
                $this->template = \preg_replace("/$pattern/", $data[$var], $this->template, 1);
                $this->replaceData($data);
            }
        }
    }

    public function compile($data)
    {
        $pattern = "\@include\((?:\'|\")(?<content>.*)(?:\'|\")\)\;";
        $this->template = preg_replace("/{$this->layoutPattern}/", "", $this->template);

        if (preg_match("/$pattern/", $this->template, $matches)) {
            $content = ViewProvider::getContent($matches['content']);
            $view = new View($content);
            $view->compile($data);
            $this->replaceData($data);
            $this->template = preg_replace("/$pattern/", $view->template, $this->template, 1);
            $this->compile($data);
        }

        return $this->template;
    }
}
