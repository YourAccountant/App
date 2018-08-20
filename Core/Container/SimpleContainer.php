<?php

namespace Core\Container;

class SimpleContainer extends Container
{

    /**
     * Get key
     * @param string key
     * @return mixed item
     */
    public function get($key)
    {
        if (!$this->has($key)) {
            throw new NotFound("$key not found");
        }

        return $this->instances[$key];
    }

    public function run($key, $args = [])
    {
        throw new \Exception("Simple container is not ment to be ran");
    }
}
