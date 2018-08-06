<?php

namespace Core\Foundation;

class Model
{

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
