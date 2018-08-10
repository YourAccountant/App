<?php

namespace Core\Database\Migration;

class Table
{

    public $table;

    public $columns;

    public $charset = 'utf8';

    public $engine = "INNODB";

    public $primary;

    public $uniques = [];

    public $indexes = [];

    public $relations = [];

    public $hasBeenPrepared = false;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function add($name, $type = null, $length = null)
    {
        $column = new Column($name, $type, $length);
        $this->columns[] = $column;
        return $column;
    }

    public function prepare()
    {
        foreach ($this->columns as $column) {
            if ($column->primary) {
                $this->primary = $column->name;
            }

            if ($column->unique) {
                $this->uniques[] = $column->name;
            }

            if ($column->index) {
                $this->indexes[] = $column->name;
            }

            if (!empty($column->relations)) {
                $this->relations[$column->name] = $column->relations;
            }
        }

        $this->hasBeenPrepared = true;
        return $this;
    }

    public function create()
    {
        if (!$this->hasBeenPrepared) {
            $this->prepare();
        }

        $sql = " CREATE TABLE {$this->table} ( \n";
        foreach ($this->columns as $column) {
            $sql .= "\t" . trim($column->generate()) . ",\n";
        }

        if ($this->primary != null) {
            $sql .= "\tPRIMARY KEY ($this->primary), \n";
        }

        if (!empty($this->uniques)) {
            $sql .= "\tUNIQUE (".trim(implode(',', $this->uniques), ',')."), \n";
        }

        if (!empty($this->indexes)) {
            $sql .= "\tINDEX (".trim(implode(',', $this->indexes), ',')."), \n";
        }

        foreach ($this->relations as $name => $columnRelations) {
            foreach ($columnRelations as $relation) {
                list($fkTable, $fkColumn) = explode('.', $relation);
                $sql .= "\tFOREIGN KEY ({$name}) REFERENCES {$fkTable}({$fkColumn}), \n";
            }
        }

        $sql = rtrim(trim($sql), ',');

        $sql .= "\n) ENGINE={$this->engine} CHARACTER SET {$this->charset};\n";

        return $sql;
    }
}
