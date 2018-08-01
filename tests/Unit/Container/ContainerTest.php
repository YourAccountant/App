<?php

namespace Test\Unit\Container;

use \Core\Container\Container;
use \Core\Container\QueueContainer;
use \Core\Cache\Cache;
use \Core\Cache\CacheItem;

use \PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{

    public function testContainer()
    {
        $containerItem = new Cache(__DIR__ . "/../../data/temp/");

        $container = new Container();
        $container->add("test", $containerItem);
        $item = $container->get("test");
        $this->assertSame($containerItem, $item);
        $container->delete("test");
        $this->assertSame(false, $container->has("test"));
    }

    public function testQueueContainer()
    {
        $containerItem = new Cache(__DIR__ . "/../../data/temp/");

        $container = new Container();
        $container->add("cache", $containerItem);

        $cacheItem1 = new CacheItem("test1", ["hello" => "world"]);
        $cacheItem2 = new CacheItem("test2", ["hello" => "world"]);

        $queue = new QueueContainer($container);
        $queue->add("cache.saveDeferred", [$cacheItem1]);
        $queue->add("cache.saveDeferred", [$cacheItem2]);
        $queue->add("cache.commit");
        $queue->add("cache.clear");
        $queue->commit();

        $final = $container->get("cache")->hasItem("test1");

        $this->assertSame(false, $final);
    }
}
