<?php

namespace Core\View;

class Layout
{

    public function __construct($template)
    {
        $this->template = $template;
    }

    public function compile(View $view, $data)
    {
        $content = $view->compile($data);
        $this->template = preg_replace("/\@setContent\(\);/", $content, $this->template);
        return $this->template;
    }

}
