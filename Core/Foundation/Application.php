<?php

namespace Core\Foundation;

use \Core\Config\Config;
use \Core\Cache\Cache;
use \Core\Container\Container;
use \Core\Router\Router;
use \Core\Debug\Debug;
use \Core\Database\Connection;
use \Core\Database\Migration\Migration;
use \Core\View\ViewProvider;

class Application
{
    const VERSION = '0.1';

    public static $instance;

    protected $root;

    protected $dependencies;

    protected $services;

    protected $controllers;

    protected $commands;

    protected $policies;

    protected $misc;

    public function __construct($root, $auto = true)
    {
        self::$instance = $this;

        $this->root = $root;
        $this->dependencies = new Container();
        $this->services = new Container();
        $this->controllers = new Container();
        $this->policies = new Container();
        $this->commands = new Container();
        $this->misc = new Container();

        if ($auto) {
            $this->initialize();
        }
    }

    public function initialize()
    {
        $this->setConfig(".config");
        $this->setApp("App");
        $this->setCache(".cache");
        $this->setView("views", "layout.php");
        $this->setConnection();
        $this->setDebug("log");
        $this->setMisc();
        return $this;
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

    public function setView($path, $layout = null)
    {
        $this->views = new ViewProvider(rtrim($this->root, '/') . '/' . trim($path, '/'), $layout);
        return $this;
    }

    public function setDebug($path)
    {
        $instance = new Debug($path);
        $instance->setApp($this);
        $this->services->add('Debugger', $instance);
        return $this;
    }

    public function setConnection()
    {
        Connection::boot($this->dependencies->get('Config'));
        $this->dependencies->add('Connection', new Connection());
        return $this;
    }

    public function setApp($path)
    {
        $this->scanApp($this->root . '/' . trim($path, '/'));
        return $this;
    }

    public function setMisc()
    {
        Migration::setConnection($this->dependencies->get('Connection'));
        Router::setControllers($this->controllers);
        Router::setPolicies($this->policies);
        return $this;
    }

    private function addService($namespace)
    {
        $classname = explode("\\", $namespace);
        $classname = end($classname);

        $name = $instance->alias ?? $classname;

        if (is_subclass_of($namespace, Service::class)) {
            $this->services->add($name, $namespace);
        } elseif (is_subclass_of($namespace, Policy::class)) {
            $this->policies->add($name, $namespace);
        } elseif (is_subclass_of($namespace, Controller::class)) {
            $this->controllers->add($name, $namespace);
        } elseif (is_subclass_of($namespace, Command::class)) {
            $this->commands->add($name, $namespace);
        } else {
            return false;
        }

        return true;
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

            $this->addService($namespace);
        }
    }

    public function run()
    {
        Router::dispatch();
    }
}
