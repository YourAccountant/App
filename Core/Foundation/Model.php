<?php

namespace Core\Foundation;

use \Core\Database\Connection;
use \Core\Database\QueryBuilder;
use \Core\Support\Str;

class Model extends Bootable
{
    /**
     * Fetched database object
     * @var object
     */
    private $pool;

    private $builder;

    protected $table;

    public function __construct($id = null)
    {
        if ($id != null) {
            $this->getBy("id", "=", $id);
        }
    }

    public function getBy($column, $operator, $value)
    {
        $this->builder = Connection::builder($this->getTableName());
        $this->builder->where($column, $operator, $value);
        $query = $this->builder->exec();
        $model = $query->fetch();
        $this->pool = $model;
        $this->setModelByPool();

        if (empty($model)) {
            return false;
        }

        return true;
    }

    public function setModelByPool()
    {
        if (empty($this->pool)) {
            return $this;
        }

        foreach ($this->pool as $key => $value) {
            $this->{$key} = $value;
        }
        return $this;
    }

    public function getTableName()
    {
        if ($this->table == null) {
            $this->table = Str::camelCaseToSnakeCase(static::class) . 's';
        }

        return $this->table;
    }

    public function getWithoutIgnore()
    {
        $pool = $this->pool;
        if (isset($this->ignore)) {
            foreach ($this->ignore as $column) {
                if (isset($pool->$column)) {
                    unset($pool->$column);
                }
            }
        }

        return $pool;
    }

    public function __toString()
    {
        return json_encode($this->getWithoutIgnore());
    }
}
