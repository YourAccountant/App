<?php

namespace Core\Foundation;

class Service
{

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

}
