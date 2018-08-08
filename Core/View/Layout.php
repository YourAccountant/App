<?php

namespace Core\View;

class Layout
{

    public function __construct($template)
    {
        $this->template = $template;
    }

    public function compile(View $view)
    {
        $content = $view->compile();
        $this->template = preg_replace("/\@setContent\(\);/", $content, $this->template);
        return $this->template;
    }

}
