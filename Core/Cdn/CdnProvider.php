<?php

namespace Core\Cdn;

use \Core\Router\Request;

class CdnProvider extends \Core\Foundation\Service
{

    public $url;

    public function boot()
    {
        $path = $this->getDependencies()->get('Config')->cdn;

        if ($this->url == null) {
            $this->url = Request::getHost() . '/' . trim($path, '/');
            $this->path = $this->getRoot() . '/' . trim($path, '/');
        }

        return $this;
    }

    public function add($name, $item)
    {
        file_put_contents($this->path . '/' . trim($name, '/'), $item);
    }

    public function remove($name)
    {
        unlink($this->path . '/' . trim($name, '/'));
    }

    public function get($name)
    {
        return \file_get_contents($this->path . '/' . trim($name, '/'));
    }
}
