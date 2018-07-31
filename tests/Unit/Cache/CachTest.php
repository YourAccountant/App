<?php

namespace Test\Unit\Cache;

use \Core\Cache\Cache;
use \Core\Cache\CacheItem;

use \PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    public function testCache()
    {
        $cacheItem = new CacheItem("test", ["hello" => "world"]);

        $cache = new Cache(__DIR__ . "/../../data/temp/");
        $cache->save($cacheItem);

        $this->assertSame($cacheItem, $cache->getItem("test"));

        $cache->deleteItem("test");

        $removed = $cache->hasItem("test");
        $this->assertSame(false, $removed);
    }

    public function testCacheMulti()
    {
        $cacheItem1 = new CacheItem("test1", ["hello" => "world"]);
        $cacheItem2 = new CacheItem("test2", ["hello" => "world"]);
        $cacheItem3 = new CacheItem("test3", ["hello" => "world"]);

        $cache = new Cache(__DIR__ . "/../../data/temp/");
        $cache->saveDeferred($cacheItem1);
        $cache->saveDeferred($cacheItem2);
        $cache->saveDeferred($cacheItem3);
        $cache->commit();

        $has = $cache->hasItem("test1") && $cache->hasItem("test2") && $cache->hasItem("test3");
        $this->assertTrue($has);

        $cache->deleteItems(["test1", "test2", "test3"]);
        $has = $cache->hasItem("test1") && $cache->hasItem("test2") && $cache->hasItem("test3");
        $this->assertSame(false, $has);
    }

    public function testClearCache()
    {
        $cacheItem1 = new CacheItem("test1", ["hello" => "world"]);
        $cacheItem2 = new CacheItem("test2", ["hello" => "world"]);
        $cacheItem3 = new CacheItem("test3", ["hello" => "world"]);

        $cache = new Cache(__DIR__ . "/../../data/temp/");
        $cache->saveDeferred($cacheItem1);
        $cache->saveDeferred($cacheItem2);
        $cache->saveDeferred($cacheItem3);
        $cache->commit();

        $cache->clear();

        $this->assertSame(false, file_exists(__DIR__ . "/../../data/temp/test1.cache"));
    }
}
