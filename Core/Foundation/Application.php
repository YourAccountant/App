<?php

namespace Core\Foundation;

use \Core\Config\Config;
use \Core\Cache\Cache;
use \Core\Container\Container;
use \Core\Router\Router;

class Application
{
    const VERSION = '0.1';

    protected $root;

    protected $services;

    protected $controllers;

    protected $commands;

    protected $policies;

    protected $caught;

    protected $config;

    protected $cache;

    public function __construct($root)
    {
        $this->root = $root;
        $this->dependencies = new Container();
        $this->services = new Container();
        $this->controllers = new Container();
        $this->policies = new Container();
        $this->commands = new Container();
        $this->caught = new Container();
    }

    public function setCache($path)
    {
        $this->services->add('Cache', new \Core\Cache\Cache($this->root . '/' . trim($path, '/')));
        return $this;
    }

    public function setConfig($path)
    {
        $this->dependencies->add('Config', new \Core\Config\Config($this->root . '/' . trim($path, '/')));
        return $this;
    }

    public function setView($path)
    {
        $this->views = null; // new ViewProvider($path);
        return $this;
    }

    public function setApp($path)
    {
        $this->scanApp($this->root . '/' . trim($path, '/'));
        return $this;
    }

    private function addService($namespace)
    {
        $classname = explode("\\", $namespace);
        $classname = end($classname);

        $instance = new $namespace();
        $instance->setApp($this);

        $name = $instance->alias ?? $classname;

        if (is_subclass_of($namespace, Service::class)) {
            $this->services->add($name, $instance);
        } elseif (is_subclass_of($namespace, Policy::class)) {
            $this->policies->add($name, $instance);
        } elseif (is_subclass_of($namespace, Controller::class)) {
            $this->controllers->add($name, $instance);
        } elseif (is_subclass_of($namespace, Command::class)) {
            $this->commands->add($name, $instance);
        } else {
            $this->caught->add($name, $instance);
        }

        return $instance;
    }

    public function getNamespace($path)
    {
        $namespace = str_replace($this->root, '', $path);
        $namespace = str_replace("/", "\\", $namespace);
        return "\\" . trim($namespace, "\\");
    }

    private function scanApp($path)
    {
        $files = \scandir($path);
        foreach ($files as $key => $filename) {
            if (in_array($key, [0, 1])) {
                continue;
            }

            if (is_dir($path . '/' . $filename)) {
                $this->scanApp($path . '/' . $filename);
            }

            if (!is_file($path . '/' . $filename)) {
                continue;
            }


            if (substr($filename, -4, 4) != '.php') {
                continue;
            }

            $classname = rtrim($filename, '.php');
            $namespace = $this->getNamespace($path . "/" . $classname);

            if (!class_exists($namespace)) {
                continue;
            }

            $instance = $this->addService($namespace);

            if (method_exists($instance, "boot")) {
                $instance->boot();
            }
        }
    }

    public function setViews()
    {
        return $this;
    }

    public function run()
    {
        Router::setControllers($this->controllers);
        Router::setPolicies($this->policies);
        Router::dispatch();
    }
}
