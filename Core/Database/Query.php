<?php

namespace Core\Database;

class Query
{

    private $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

}
