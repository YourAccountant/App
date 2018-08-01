<?php

namespace Core\Container;

use \Core\Contract\Container\ContainerContract;

class Container implements ContainerContract
{
    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @param string $key
     * @param object
     * @return self
     */
    public function add(string $key, $instance)
    {
        $this->instances[$key] = $instance;
        return $this;
    }

    /**
     * @param string $key
     * @param array $args
     * @return mixed
     */
    public function run($key, $args = [])
    {
        list($class, $method) = preg_split("/\@|\./", $key);

        $instance = $this->get($class);
        return \call_user_func_array([$instance, $method], $args);
    }

    /**
     * @param string $key
     * @return object
     */
    public function get($key)
    {
        if (!$this->has($key)) {
            throw new NotFound("$key not found");
        }

        return $this->instances[$key];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        if (!isset($this->instances[$key])) {
            return false;
        }

        return true;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        if (!$this->has($key)) {
            return false;
        }

        unset($this->instances[$key]);
        return true;
    }
}
