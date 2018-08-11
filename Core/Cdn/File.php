<?php

namespace Core\Cdn;

class File
{

    public $path;

    public $content;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function get()
    {
        return file_get_contents($this->path);
    }

    public function include()
    {
        include $this->path;
        return $this;
    }

    public function remove()
    {
        \unlink($this->path);
        return $this;
    }

    public function add($content)
    {
        $this->content = $content;
        \file_put_contents($this->path, $this->content);
        return $this;
    }

    public static function unlink($path)
    {
        \unlink($path);
    }

    public static function put($path, $content)
    {
        \file_put_contents($path, $content);
    }
}
