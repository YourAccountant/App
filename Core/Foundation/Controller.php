<?php

namespace Core\Foundation;

class Controller
{

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
