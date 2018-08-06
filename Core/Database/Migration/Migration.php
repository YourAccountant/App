<?php

namespace Core\Database\Migration;

class Migration
{

    public $table;

    public $charset;

    public $columns;

    public $engine = "INNODB";

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

    public function getSql()
    {
        $primary = "";
        $indexes = [];
        $relations = [];
        $sql = " CREATE TABLE {$this->table} ( \n";
        foreach ($this->columns as $column) {
            if ($column->primary) {
                $primary = $column->name;
            }

            if ($column->index) {
                $indexes[] = $column->name;
            }

            if (!empty($column->relations)) {
                $relations[$column->name] = $column->relations;
            }

            $sql .= $column->generate() . ",\n";
        }

        if ($primary != null) {
            $sql .= " PRIMARY KEY ($primary), \n";
        }

        if (!empty($indexes)) {
            $sql .= " INDEX (".trim(implode(',', $indexes), ',')."), \n";
        }

        foreach ($relations as $name => $columnRelations) {
            foreach ($columnRelations as $relation) {
                list($fkTable, $fkColumn) = explode('.', $relation);
                $sql .= " FOREIGN KEY ({$name}) REFERENCES {$fkTable}({$fkColumn}), \n";
            }
        }

        $sql = rtrim(trim($sql), ',');

        $sql .= ") ENGINE={$this->engine};\n";

        return $sql;
    }

}
