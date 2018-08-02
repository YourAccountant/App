<?php

namespace Core\Contract\Router;

interface RouteContract
{
    public function __construct(string $method, string $route, array $middleware = [], $callback);

    public function match($path);
}
