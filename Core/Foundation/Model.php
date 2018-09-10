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
    protected $pool;

    protected $table;

    public function poolIsEmpty()
    {
        return empty($this->pool) ? true : false;
    }

    public function get($property = null)
    {
        if ($property == null) {
            return $this->pool;
        }

        if (isset($this->pool->$property)) {
            return $this->pool->$property;
        }

        return null;
    }

    public function set($property, $value)
    {
        if (!is_object($this->pool)) {
            $this->pool = new \stdClass();
        }

        $this->pool->$property = $value;
        return $this;
    }

    public function insert($data)
    {
        return $this->getDependencies('Connection')
                ->builder($this->getTableName())
                ->insert($data)
                ->exec();
    }

    public function update($id, $data)
    {
        $this->getDependencies('Connection')
                ->builder($this->getTableName())
                ->where('id', '=', $id)
                ->update($data)
                ->exec();

        return $id;
    }

    public function delete($id)
    {
        return $this->getDependencies('Connection')
                ->builder($this->getTableName())
                ->where('id', '=', $id)
                ->delete()
                ->exec();
    }

    public function getBy($column, $operator, $value)
    {
        $model = $this->getDependencies('Connection')
                    ->builder($this->getTableName())
                    ->where($column, $operator, $value)
                    ->exec()
                    ->fetch();

        $this->pool = $model;

        if (empty($model)) {
            return false;
        }

        return true;
    }

    public function exists($column, $operator, $value)
    {
        $count = $this->getDependencies('Connection')
                    ->builder($this->getTableName())
                    ->columns("COUNT({$column}) as `total`")
                    ->where($column, $operator, $value)
                    ->limit(1)
                    ->exec()
                    ->fetch();

        return $count->total > 0 ? true : false;
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
