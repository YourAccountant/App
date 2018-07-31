<?php

namespace Core\Cache;

use \Core\Contract\Cache\CacheItemContract;
use \Core\Contract\Cache\CachePoolContract;

class Cache implements CachePoolContract
{

    private $cacheItems = [];

    private $deferred = [];

    private $path;

    public function __construct(string $path)
    {
        $this->path = rtrim($path, "/");

        if (!file_exists($this->path)) {
            mkdir($this->path, 0777, true);
        }
    }

    public function getItem($key)
    {
        if (!$this->hasItem($key)) {
            return false;
        }

        if (isset($this->cacheItems[$key])) {
            return $this->cacheItems[$key];
        }

        $content = unserialize(file_get_contents($this->path . "/" . $key));
        $cacheItem = new CacheItem($key, $content);
        $this->cacheItems[$key] = $cacheItem;

        return $cacheItem;
    }

    public function getItems($keys = [])
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->getItem($key);
        }

        return $result;
    }

    public function hasItem($key)
    {
        if (isset($this->cacheItems[$key])) {
            return true;
        }

        if (file_exists($this->path . "/" . $key)) {
            return true;
        }

        return false;
    }

    public function clear($path = null)
    {
        $path = $path ?? $this->path;
        $files = \scandir($path);
        foreach ($files as $file) {
            if (\is_file($file)) {
                unset($file);
            } else if (\is_dir($file)) {
                $this->clear($path . "/" . $file);
            }
        }

        return $this;
    }

    public function deleteItem($key)
    {
        if (!$this->hasItem($key)) {
            return false;
        }

        $file = $this->path . "/" . $key;

        if (!is_file($file)) {
            return false;
        }

        unlink($file);

        if (isset($this->cacheItems[$key])) {
            unset($this->cacheItems[$key]);
        }

        return true;
    }

    public function deleteItems($keys = [])
    {
        $success = true;
        foreach ($keys as $key) {
            if (!$this->deleteItem($key)) {
                $success = false;
            }
        }

        return $success;
    }

    public function save(CacheItemContract $item)
    {
        if ($this->hasItem($item->getKey())) {
            return false;
        }

        $key = $item->getKey();
        $contents = $item->get();

        \file_put_contents($this->path . "/" . $key, serialize($contents));
        $this->cacheItems[$key] = $item;

        return true;
    }

    public function saveDeferred(CacheItemContract $item)
    {
        $this->deferred[] = $item;
        return $this;
    }

    public function clearDeferred()
    {
        $this->deferred = [];
        return $this;
    }

    public function commit()
    {
        $success = true;
        foreach ($this->deferred as $item) {
            if (!$this->save($item)) {
                $success = false;
            }
        }

        $this->deferred = [];

        return $success;
    }

}
