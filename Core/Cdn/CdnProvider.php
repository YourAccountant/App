<?php

namespace Core\Cdn;

use \Core\Router\Request;

class CdnProvider extends \Core\Foundation\Service
{

    public $url;

    public $cryption;

    public $files = [];

    public function boot()
    {
        $this->path = $this->getApp()->getPath('cdn') ?? $this->getRoot() . '/' . 'public/cdn';

        $this->cryption = function ($name) {
            return $name;
        };

        if (!file_exists($this->path)) {
            mkdir($this->path, 0777, true);
        }

        return $this;
    }

    public function getFullPath($name)
    {
        return $this->path . '/' . trim($name, '/');
    }

    public function add($name, $content)
    {
        $file = new File($this->getFullPath($name));
        $this->files[$name] = $file;
        $file->add($content);
        return $file;
    }

    public function remove($name)
    {
        File::unlink($name);
        return $name;
    }

    public function get($name)
    {
        $last = explode('/', $name);
        $last = end($last);

        return \file_get_contents($this->path . '/' . trim($name, '/'));
    }

    public function hash($name)
    {
        $method = $this->cryption;
        return $method($name);
    }

    public function setCryption($callback)
    {
        $this->cryption = $callback;
        return $this;
    }
}
