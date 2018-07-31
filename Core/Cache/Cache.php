<?php

namespace Core\Cache;

use \Core\Contract\Cache\CacheItemContract;
use \Core\Contract\Cache\CachePoolContract;

class Cache implements CachePoolContract
{
    /**
     * @var array \Core\Contract\Cache\CacheItemContract
     */
    private $cacheItems = [];

    /**
     * @var array \Core\Contract\Cache\CacheItemContract
     */
    private $deferred = [];

    /**
     * @var string
     */
    private $path;

    /**
     * @param string $path
     * @return self
     */
    public function __construct(string $path)
    {
        $this->path = rtrim($path, "/");

        if (!file_exists($this->path)) {
            mkdir($this->path, 0777, true);
        }
    }

    /**
     * @param string $key
     * @return object \Core\Contract\Cache\CacheItemContract
     */
    public function getItem($key)
    {
        if (!$this->hasItem($key)) {
            return false;
        }

        if (isset($this->cacheItems[$key])) {
            return $this->cacheItems[$key];
        }

        $content = unserialize(file_get_contents($this->path . "/" . $key . ".cache"));
        $cacheItem = new CacheItem($key, $content);
        $this->cacheItems[$key] = $cacheItem;

        return $cacheItem;
    }

     /**
     * @param array $keys
     * @return array \Core\Contract\Cache\CacheItemContract
     */
    public function getItems($keys = [])
    {
        $result = [];

        if (!empty($keys)) {
            foreach ($keys as $key) {
                $result[$key] = $this->getItem($key);
            }
        } else {
            $result = $this->cacheItems;
        }

        return $result;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasItem($key)
    {
        if (isset($this->cacheItems[$key])) {
            return true;
        }

        if (file_exists($this->path . "/" . $key . ".cache")) {
            return true;
        }

        return false;
    }

    /**
     * @param string $path (optional)
     * @return self
     */
    public function clear()
    {
        $files = \scandir($this->path);

        foreach ($files as $file) {
            $file = $this->path . "/" . $file;
            if (\is_file($file)) {
                unlink($file);
            }
        }

        $this->cacheItems = [];

        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function deleteItem($key)
    {
        if (!$this->hasItem($key)) {
            return false;
        }

        $file = $this->path . "/" . $key . ".cache";

        if (!is_file($file)) {
            return false;
        }

        unlink($file);

        if (isset($this->cacheItems[$key])) {
            unset($this->cacheItems[$key]);
        }

        return true;
    }

    /**
     * @param array keys
     * @return bool
     */
    public function deleteItems($keys = [])
    {
        if (empty($keys)) {
            $this->clear();
            return true;
        }

        $success = true;
        foreach ($keys as $key) {
            if (!$this->deleteItem($key)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * @param object \Core\Contract\Cache\CacheItemContract $item
     * @return bool
     */
    public function save(CacheItemContract $item)
    {
        if ($this->hasItem($item->getKey())) {
            return false;
        }

        $key = $item->getKey();
        $contents = $item->get();

        \file_put_contents($this->path . "/" . $key . ".cache", serialize($contents));
        $this->cacheItems[$key] = $item;

        return true;
    }

    /**
     * @param object \Core\Contract\Cache\CacheItemContract $item
     * @return self
     */
    public function saveDeferred(CacheItemContract $item)
    {
        $this->deferred[] = $item;
        return $this;
    }

    /**
     * @return self
     */
    public function clearDeferred()
    {
        $this->deferred = [];
        return $this;
    }

    /**
     * @return bool
     */
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
