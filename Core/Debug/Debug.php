<?php

namespace Core\Debug;

use \Core\Contract\Foundation\DependsOnApp;
use \Core\Foundation\Application;

class Debug implements DependsOnApp
{

    private $app;

    private $path;

    private $queryDebugger;

    private $logger;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function setApp(Application $app)
    {
        $this->app = $app;
        return $this;
    }

}
