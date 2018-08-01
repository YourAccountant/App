<?php

namespace Core\Router;

use \Core\Contract\Router\DispatcherContract;
use \Core\Contract\Router\RouterContract;
use \Core\Contract\Container\ContainerContract;

/*
run
match
execRouter
createParams
execMiddleware
response->run
execAfter
response->setBack
*/
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
        $this->response = new Response();
        foreach ($this->router->routes[$this->request->method] as $route) {
            if (!$this->match($route)) {
                continue;
            }

            $this->execRoute($route);
            $this->response->run();
            break;
        }

        if (!$this->response->hasResponse()) {
            $this->response->code = 404;
        }

        $this->execAfter();
        $this->response->setBack($this->request->fullUrl);
    }

    public function execAfter()
    {
        $code = $this->response->code;
        $call = $this->router->on[$code] ?? null;
        if ($call != null) {
            if (is_callable($call)) {
                $call($this->request, $this->response);
            } else {
                $this->services->run($call, [$this->request, $this->response]);
            }
        }
    }

    public function execRoute($route)
    {
        $this->createParams($route);
        $this->execMiddleware($route->middleware);

        if (!$this->response->hasResponse())
        {
            $callback = $route->callback;
            if (is_callable($callback)) {
                $callback($this->request, $this->response);
            } else {
                $this->controllers->run($callback, [$this->request, $this->response]);
            }
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

                if (!$this->response->hasResponse()) {
                    break;
                }
            }
        }
    }

    public function createParams($route)
    {
        $params = [];
        $keys = explode('/', $this->request->path);
        foreach ($keys as $key => $value) {
            if (isset($route->params[$key])) {
                $params[$route->params[$key]] = $value;
            }
        }

        $this->request->params = $params;

        return $params;
    }

    public function match($route)
    {
        $path = $this->request->path == "/" ? "/" : '/' . trim($this->request->path, '/') . '/';
        return preg_match("/^{$route->pattern}$/", $path) ? true : false;
    }

}
