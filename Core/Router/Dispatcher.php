<?php

namespace Core\Router;

use \Core\Contract\Router\DispatcherContract;
use \Core\Contract\Router\RouterContract;
use \Core\Contract\Container\ContainerContract;
use \Core\Debug\Debug;
use \Core\Foundation\Application;

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
    private $routes;

    private $controllers;

    public $request;

    public $response;

    public $found = false;

    public function __construct($routes, $controllers = null, $policies = null, $on = null)
    {
        if (!isset($_SESSION['app']['back'])) {
            $_SESSION['app']['back'] = '/';
        }

        $this->routes = $routes;
        $this->controllers = $controllers;
        $this->policies = $policies;
        $this->on = $on;
        $this->request = new Request();

        Application::getInstance()->addModel('request', $this->request);
    }

    public function run()
    {
        if (strpos($this->request->path, 'favicon.ico') !== false) {
            return;
        }

        Response::addHistory();
        $this->response = new Response();
        if (isset($this->routes[$this->request->method]))
        {
            foreach ($this->routes[$this->request->method] as $route) {
                if (!$route->match($this->request->path)) {
                    continue;
                }

                Debug::add('routing', 'route', \get_object_vars($route));
                $this->found = true;
                $this->execRoute($route);
                break;
            }
        }

        if (!$this->found) {
            $this->response->code = 404;
        }

        $this->execAfter();
        $this->response->run();
        Response::setBack();
    }

    private function execAfter()
    {
        $code = $this->response->code;
        $call = $this->on[$code] ?? null;
        if ($call != null) {
            if (is_callable($call)) {
                $call($this->request, $this->response);
            } else {
                $this->policies->run($call, [$this->request, $this->response]);
            }
        }
    }

    private function execRoute($route)
    {
        $this->createParams($route);
        $this->execMiddleware($route->middleware);

        if (!$this->response->hasResponse()) {
            $callback = $route->callback;
            if (is_callable($callback)) {
                $callback($this->request, $this->response);
            } else {
                $this->controllers->run($callback, [$this->request, $this->response]);
            }
        }
    }

    private function execMiddleware($middlewares)
    {
        if (!empty($middlewares)) {
            foreach ($middlewares as $middleware) {
                if (is_callable($middleware)) {
                    $middleware($this->request, $this->response);
                } else {
                    $this->policies->run($middleware, [$this->request, $this->response]);
                }

                if (!$this->response->hasResponse()) {
                    break;
                }
            }
        }
    }

    private function createParams($route)
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
}
