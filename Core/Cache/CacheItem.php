<?php

namespace Core\Cache;

use \Core\Contract\Cache\CacheItemContract;
use \Core\Contract\Cache\CachePoolContract;

class CacheItem implements CacheItemContract
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var mixed
     */
    private $content;

    /**
     * @param string $key
     * @param mixed $content
     */
    public function __construct(string $key, $content)
    {
        $this->key = $key;
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}
