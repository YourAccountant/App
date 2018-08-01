<?php

namespace Core\Container;

use \Core\Contract\Container\ContainerContract;

class QueueContainer
{
    /**
     * @var array
     */
    private $queue = [];

    /**
     * @var object \Core\Contract\Container\ContainerContract
     */
    private $services;

    /**
     * @param object \Core\Contract\Container\ContainerContract $services
     * @return self
     */
    public function __construct(ContainerContract $services)
    {
        $this->services = $services;
    }

    /**
     * @param string $key
     * @param array $args
     * @return self
     */
    public function add($key, $args = [])
    {
        $this->queue[] = ['key' => $key, 'args' => $args];
        return $this;
    }

    /**
     * @return self
     */
    public function commit()
    {
        foreach ($this->queue as $queue) {
            $this->services->run($queue['key'], $queue['args']);
        }

        $this->clear();
        return $this;
    }

    /**
     * @return self
     */
    public function clear()
    {
        $this->queue = [];
        return $this;
    }
}
