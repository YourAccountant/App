<?php

namespace Test\Unit\Container;

use \Core\Container\Container;
use \Core\Cache\Cache;

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
}
