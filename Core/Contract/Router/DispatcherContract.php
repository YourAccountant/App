<?php

namespace Core\Contract\Router;

interface DispatcherContract
{
    public function __construct(RouterContract $router);

    public function run();
}
