<?php

namespace Core\Contract\Router;

interface DispatcherContract
{
    public function __construct($routes, $controllers = null, $policies = null, $on = null);

    public function run();
}
