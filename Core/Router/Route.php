<?php

namespace Core\Router;

use \Core\Contract\Router\RouteContract;

class Route implements RouteContract
{
    public $method;

    public $route;

    public $middleware;

    public $callback;

    public $pattern;

    public $params = [];

    public function __construct(string $method, string $route, array $middleware = [], $callback)
    {
        $this->method = $method;
        $this->route = $route;
        $this->middleware = $middleware;
        $this->callback = $callback;
        $this->setPattern();
    }

    public function setMiddleware($middleware = [])
    {
        if (is_array($middleware)) {
            $this->middleware = array_merge($this->middleware, $middleware);
        } else {
            $this->middleware[] = $middleware;
        }

        return $this;
    }

    private function setPattern()
    {
        $pattern = "";
        $params = explode('/', $this->route . '/');
        foreach ($params as $key => $value) {
            if ($value == '') {
                continue;
            }

            if ($value[0] != ':') {
                $pattern .= "\/$value";
            } else {
                $this->params[$key] = ltrim($value, ':');
                $pattern .= "\/[0-9a-zA-Z\-\_]+";
            }
        }
        $this->pattern = $pattern != "" ? $pattern : "\/";
    }

    public function match($path)
    {
        return preg_match("/^{$this->pattern}$/", '/'.trim($path, '/')) ? true : false;
    }
}
