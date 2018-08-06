<?php

namespace Core\Router;

use \Core\Contract\Router\RouterContract;
use \Core\Contract\Container\ContainerContract;

class Router implements RouterContract
{
    private static $routes = [];

    private static $controllers;

    private static $policies;

    private static $prefix = '';

    private static $middleware = [];

    private static $on = [];

    public static function setControllers($controllers)
    {
        self::$controllers = $controllers;
    }

    public static function setPolicies($policies)
    {
        self::$policies = $policies;
    }

    public static function setPrefix($prefix)
    {
        self::$prefix = '/' . trim($prefix, '/');
    }

    public static function setMiddleware($middleware = [])
    {
        self::$middleware = $middleware;
    }

    public static function addRoute($method, $route, $callback)
    {
        $instance = new Route($method, self::$prefix . '/' . trim($route, '/'), self::$middleware, $callback);
        self::$routes[$method][] = $instance;

        return $instance;
    }

    public static function dispatch()
    {
        $dispatcher = new Dispatcher(self::$routes, self::$controllers, self::$policies, self::$on);
        $dispatcher->run();
    }

    public static function on($event, $callback)
    {
        self::$on[$event] = $callback;
    }

    public static function get($route, $callback)
    {
        return self::addRoute('GET', $route, $callback);
    }

    public static function post($route, $callback)
    {
        return self::addRoute('POST', $route, $callback);
    }

    public static function put($route, $callback)
    {
        return self::addRoute('PUT', $route, $callback);
    }

    public static function patch($route, $callback)
    {
        return self::addRoute('PATCH', $route, $callback);
    }

    public static function delete($route, $callback)
    {
        return self::addRoute('DELETE', $route, $callback);
    }

}
