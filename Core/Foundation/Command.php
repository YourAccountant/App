<?php

namespace Core\Foundation;

abstract class Command
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->$app = $app;
    }

    abstract public function run($data = null);
}
