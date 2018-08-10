<?php

namespace Core\Debug;

use \Core\Database\Connection;

class DatabaseDebugger
{

    public function __construct()
    {
        Connection::addHook('connect', function ($success) {
            $this->add('connect', compact('success'));
        });

        Connection::addHook('query', function ($sql, $prepares) {
            $this->add('query', compact('sql', 'prepares'));
        });
    }

    public function add($name, $params)
    {
        Debug::add("Database", $name, $params);
    }
}
