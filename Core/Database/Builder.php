<?php

namespace Core\Database;

class Builder
{
    private $connection;

    private $table;

    public function __construct($connection, $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }
}
