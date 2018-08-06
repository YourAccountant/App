<?php

namespace Core\Foundation;

use \Core\Contract\Foundation\DependsOnApp;

class Policy implements DependsOnApp
{

    protected $app;

    public function setApp(Application $app)
    {
        $this->app = $app;
        return $this;
    }
}
