<?php

namespace Core\Container;

use \Core\Contract\Container\ContainerContract;

class QueueContainer
{
    private $queue = [];

    private $services;

    public function __construct(ContainerContract $services)
    {
        $this->services = $services;
    }

    public function add($key, $args = [])
    {
        $this->queue[] = ['key' => $key, 'args' => $args];
        return $this;
    }

    public function commit()
    {
        foreach ($this->queue as $queue)
        {
            $this->services->run($queue['key'], $queue['args']);
        }

        $this->clear();
        return $this;
    }

    public function clear()
    {
        $this->queue = [];
        return $this;
    }
}
