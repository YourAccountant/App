<?php

namespace Core\Foundation;

use \Core\Config\Config;
use \Core\Cache\Cache;
use \Core\Container\Container;
use \Core\Container\SimpleContainer;
use \Core\Router\Router;
use \Core\Debug\Debug;
use \Core\Debug\Logger;
use \Core\Database\Connection;
use \Core\Database\Migration\Migration;
use \Core\Template\View;
use \Core\Cdn\CdnProvider;

class Application
{
    const VERSION = '0.1';

    public static $instance;

    public $root;

    public $dependencies;

    public $services;

    public $controllers;

    public $commands;

    public $policies;

    public $models;

    public $misc;

    public function __construct($root, $auto = true)
    {
        self::$instance = $this;

        $this->root = $root;
        $this->dependencies = new Container();
        $this->services = new Container();
        $this->controllers = new Container();
        $this->policies = new Container();
        $this->commands = new Container();
        $this->models = new SimpleContainer();
        $this->misc = new Container();

        if ($auto) {
            $this->initialize();
        }
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    public function initialize()
    {
        $this->setPaths();
        $this->setDebug();
        $this->setConfig();
        $this->setApp();
        $this->setCdnProvider();
        $this->setCache();
        $this->setConnection();
        $this->setMisc();
        return $this;
    }

    public function setPaths()
    {
        $this->paths = json_decode(file_get_contents($this->root . '/' . 'config.json'))->paths;
        return $this;
    }

    public function getPath($name)
    {
        return $this->root . '/' . trim($this->paths->{$name}, '/');
    }

    public function setCache()
    {
        $this->services->add('Cache', new \Core\Cache\Cache($this->getPath('cache')));
        return $this;
    }

    public function setConfig()
    {
        $this->dependencies->add('Config', new \Core\Config\Config($this->getPath('config') ?? ".config"));
        return $this;
    }

    public function setDebug()
    {
        $instance = new Debug($this->getPath('log'));
        $this->services->add('Debugger', $instance);
        return $this;
    }

    public function setConnection()
    {
        Connection::boot($this->dependencies->get('Config'));
        $this->dependencies->add('Connection', new Connection());
        return $this;
    }

    public function setCdnProvider()
    {
        $cdn = new CdnProvider();
        $cdn->boot();
        $this->dependencies->add('Cdn', $cdn);
        return $this;
    }

    public function setApp()
    {
        $this->scanApp($this->getPath('app'));
        return $this;
    }

    public function setMisc()
    {
        View::setBase($this->root . '/views');
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

    public function addModel($name, $model)
    {
        $this->models->add($name, $model);
        return $this;
    }

    public function getModel($name)
    {
        return $this->models->get($name);
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

    public function isConsole()
    {
        return php_sapi_name() == 'cli';
    }

    public function run()
    {
        if (!$this->isConsole()) {
            Router::dispatch();
        }
    }
}
