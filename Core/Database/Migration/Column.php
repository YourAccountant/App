<?php

namespace Core\Database\Migration;

class Column
{

    public $name;

    public $type;

    public $length;

    public $default;

    public $unsigned = false;

    public $primary = false;

    public $index = false;

    public $relations = [];

    public $nullable = false;

    public $unique = false;

    public $sql = "";

    public function __construct($name, $type = null, $length = null)
    {
        $this->name = $name;

        if ($type != null) {
            $this->type = strtoupper($type);
        }

        if ($length != null) {
            $this->length = $length;
        }
    }

    public function bool($default = true)
    {
        $this->type = 'TINYINT';
        $this->length = 1;
        $this->default = $default ? 1 : 0;
        return $this;
    }

    public function string($lenght = 255)
    {
        $this->type = 'VARCHAR';
        $this->length = $lenght;
        return $this;
    }

    public function int($length = 11)
    {
        $this->type = 'INT';
        $this->length = $length;
        return $this;
    }

    public function text()
    {
        $this->type = 'TEXT';
        $this->length = null;
        return $this;
    }

    public function id($bPrimary = true)
    {
        $this->type = 'INT';
        $this->length = 11;
        if ($bPrimary) {
            $this->primary = true;
        }
        $this->nullable = false;
        $this->index = false;
        $this->unsigned = true;
        return $this;
    }

    public function unsigned()
    {
        $this->unsigned = true;
        return $this;
    }

    public function unique()
    {
        $this->unique = true;
        return $this;
    }

    public function primary()
    {
        $this->pirmary = true;
        return $this;
    }

    public function index()
    {
        $this->index = true;
        return $this;
    }

    public function relation($table, $column)
    {
        $this->relations[] = "$table.$column";
        return $this;
    }

    public function length($length)
    {
        $this->length = $length;
        return $this;
    }

    public function nullable()
    {
        $this->nullable = true;
        return $this;
    }

    public function date()
    {
        $this->type = "DATE";
        $this->length = null;
        return $this;
    }

    public function timestamp()
    {
        $this->type = "TIMESTAMP";
        $this->length = null;
        return $this;
    }

    public function dateCreate()
    {
        $this->timestamp();
        $this->default = "CURRENT_TIMESTAMP";
        return $this;
    }

    public function dateUpdate()
    {
        $this->timestamp();
        $this->default = "CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    public function generate()
    {
        $length = $this->length != null ? "({$this->length})" : "";
        $null = $this->nullable ? "" : "NOT NULL";
        $primary = $this->primary ? "AUTO_INCREMENT" : "";
        $default = $this->default != null ? "DEFAULT {$this->default}" : "";

        return " `{$this->name}` {$this->type}{$length} {$null} {$default} {$primary} ";
    }
}
