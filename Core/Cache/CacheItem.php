<?php

namespace Core\Cache;

use \Core\Contract\Cache\CacheItemContract;
use \Core\Contract\Cache\CachePoolContract;

class CacheItem implements CacheItemContract
{
    private $key;

    private $content;

    public function get()
    {
        return $this->content;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function set(string $key, array $content)
    {
        $this->key = $key;
        $this->content = $content;
        return $this;
    }
}
