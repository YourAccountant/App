<?php

namespace Core\Contract\Router;

interface RouterContract
{

    public function getServices();

    public function getControllers();

    public function dispatch();
}
