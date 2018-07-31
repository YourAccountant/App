<?php

namespace Test\Unit\Cache;

use \Core\Cache\Cache;
use \Core\Cache\CacheItem;

use \PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{

    public function testCache()
    {
        $cacheItem = new CacheItem();
        $cacheItem->set("test", ["hello" => "world"]);

        $cache = new Cache(__DIR__ . "/../../data/temp/");
        $cache->save($cacheItem);

        $this->assertSame($cacheItem, $cache->getItem("test"));

        $cache->deleteItem("test");

        $removed = $cache->hasItem("test");
        $this->assertSame(false, $removed);
    }

}
