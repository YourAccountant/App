<?php

namespace Core\Database;

class Query
{
    private $connection;

    private $builder;

    private $type;

    public $result;

    private $prepares = [];

    public $safety;

    private $stmt;

    public function __construct(Connection $connection, QueryBuilder $builder, $safety = true)
    {
        $this->safety = $safety;
        $this->connection = $connection;
        $this->builder = $builder;
        $sql = $this->route($builder->type);

        if ($sql !== false) {
            $this->stmt = $this->connection->query($sql, $this->builder->prepares);
        } else {
            throw new Exception("Safe mode is on!");
        }
    }

    public function fetchAll()
    {
        return $this->stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function fetch()
    {
        return $this->stmt->fetch(\PDO::FETCH_OBJ);
    }

    private function route($type)
    {
        switch ($type) {
            case 'select':
                return $this->select();
                break;
            case 'insert':
                return $this->insert();
                break;
            case 'update':
                if ($this->safety && empty($this->builder->where)) {
                    return false;
                }
                return $this->update();
                break;
            case 'delete':
                if ($this->safety && empty($this->builder->where)) {
                    return false;
                }
                return $this->delete();
                break;
            case 'raw':
                return $this->builder->raw;
            default:
                return;
                break;
        }
    }

    public function savePrepareData($data)
    {
        foreach ($data as $value) {
            if (is_array($value)) {
                $this->savePrepareData($value);
            } else {
                $this->prepares[] = $value;
            }
        }
    }

    public function prepare()
    {
        $this->savePrepareData($this->builder->data);
        foreach ($this->builder->where as $where) {
            list($column, $divider, $value) = $where;
            $this->prepares[] = $value;
        }
    }

    public function select()
    {
        $sql = "";
        $sql .= Generator::select($this->builder->table, $this->builder->columns);
        $sql .= Generator::join($this->builder->table, $this->builder->join);
        $sql .= Generator::where($this->builder->where);
        $sql .= Generator::orderBy($this->builder->orderBy);
        $sql .= Generator::groupBy($this->builder->groupBy);
        $sql .= Generator::limit($this->builder->limit);
        $sql .= Generator::offset($this->builder->offset);

        return $sql;
    }

    public function insert()
    {
        $sql = "";
        $sql .= Generator::insert($this->builder->table);
        $sql .= Generator::insertRows($this->builder->data);

        return $sql;
    }

    public function update()
    {
        $sql = "";
        $sql .= Generator::update($this->builder->table);
        $sql .= Generator::updateRows($this->builder->data);
        $sql .= Generator::where($this->builder->where);

        return $sql;
    }

    public function delete()
    {
        $sql = "";
        $sql .= Generator::delete($this->builder->table);
        $sql .= Generator::where($this->builder->where);

        return $sql;
    }
}
