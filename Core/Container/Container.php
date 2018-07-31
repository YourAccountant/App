<?php

namespace Core\Container;

use \Core\Contract\Container\ContainerContract;

class Container implements ContainerContract
{
    protected $instances = [];

    public function add($key, $instance)
    {
        $this->instances[$key] = $instance;
        return $instance;
    }

    public function get($key)
    {
        if (!$this->has($key)) {
            throw new NotFound("$key not found");
        }

        return $this->instances[$key];
    }

    public function has($key)
    {
        if (!isset($this->instances[$key])) {
            return false;
        }

        return true;
    }

    public function delete($key)
    {
        if (!$this->has($key)) {
            return false;
        }

        unset($this->instances[$key]);
        return true;
    }
}
