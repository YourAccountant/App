<?php

namespace Core\Router;

use \Core\Contract\Router\RouterContract;
use \Core\Contract\Container\ContainerContract;

class Router implements RouterContract
{

    private $services;

    private $controllers;

    private $prefix = '';

    private $middleware = [];

    public $on = [];

    public function __construct($controllers = null, $services = null)
    {
        $this->controllers = $controllers;
        $this->services = $services;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = '/' . trim($prefix, '/');
        return $this;
    }

    public function setMiddleware($middleware = [])
    {
        $this->middleware = $middleware;
        return $this;
    }

    public function addRoute($method, $route, $callback)
    {
        $instance = new Route($method, $this->prefix . '/' . trim($route, '/'), $this->middleware, $callback);
        $this->routes[$method][] = $instance;

        return $instance;
    }

    public function dispatch()
    {
        $dispatcher = new Dispatcher($this, $this->services, $this->controllers);
        $dispatcher->run();
    }

    public function on($code, $callback)
    {
        switch($code) {
            case 404:
                $this->on[404] = $callback;
            break;
            case 403:
                $this->on[403] = $callback;
            break;
            case 400:
                $this->on[400] = $callback;
            break;
            case 500:
                $this->on[500] = $callback;
            break;
        }

        return $this;
    }

    public function get($route, $callback)
    {
        return $this->addRoute('GET', $route, $callback);
    }

    public function post($route, $callback)
    {
        return $this->addRoute('POST', $route, $callback);
    }

    public function put($route, $callback)
    {
        return $this->addRoute('PUT', $route, $callback);
    }

    public function patch($route, $callback)
    {
        return $this->addRoute('PATCH', $route, $callback);
    }

    public function delete($route, $callback)
    {
        return $this->addRoute('DELETE', $route, $callback);
    }

}
