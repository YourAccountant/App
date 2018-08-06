<?php

namespace Core\Foundation;

use \Core\Contract\Foundation\DependsOnApp;

abstract class Command implements DependsOnApp
{
    protected $app;

    public function setApp(Application $app)
    {
        $this->app = $app;
        return $this;
    }

    abstract public function run($data = null);
}
