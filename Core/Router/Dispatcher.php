<?php

namespace Core\Router;

use \Core\Contract\Router\DispatcherContract;
use \Core\Contract\Router\RouterContract;
use \Core\Contract\Container\ContainerContract;

class Dispatcher implements DispatcherContract
{

    private $services;

    public $request;

    public $response;

    public $router;

    public function __construct(RouterContract $router, ContainerContract $services = null, ContainerContract $controllers = null)
    {
        $this->router = $router;
        $this->services = $services;
        $this->controllers = $controllers;
        $this->request = new Request();
    }

    public function run()
    {
        foreach ($this->router->routes[$this->request->method] as $route) {
            if (!$this->match($route)) {
                continue;
            }

            $this->execRoute($route);
        }

        return false;
    }

    public function execRoute($route)
    {
        $this->createResponse($route);
        $this->execMiddleware($route->middleware);

        $callback = $route->callback;
        if (is_callable($callback)) {
            $callback($this->request, $this->response);
        } else {
            $this->controllers->run($callback, [$this->request, $this->response]);
        }
    }

    public function execMiddleware($middlewares)
    {
        if (!empty($middlewares)) {
            foreach ($middlewares as $middleware) {
                if (is_callable($middleware)) {
                    $middleware($this->request, $this->response);
                } else {
                    $this->services->run($middleware, [$this->request, $this->response]);
                }
            }
        }
    }

    public function createResponse($route)
    {
        $response = new Response();
        $params = explode('/', $this->request->path);
        foreach ($params as $key => $value) {
            if (isset($route->params[$key])) {
                $response->params[$route->params[$key]] = $value;
            }
        }

        $this->request->params = $response->params;

        return $this->response = $response;
    }

    public function match($route)
    {
        return preg_match("/^{$route->pattern}$/", '/' . trim($this->request->path, '/') . '/') ? true : false;
    }

}
