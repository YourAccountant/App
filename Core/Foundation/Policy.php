<?php

namespace Core\Foundation;

class Policy
{

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
