<?php

namespace Core\Database;

class QueryBuilder
{
    private $connection;

    private $query;

    public $type = 'select';

    public $table;

    public $columns = '*';

    public $where = [];

    public $orderBy;

    public $groupBy;

    public $limit;

    public $offset;

    public $join = [];

    public $data = [];

    public function __construct($connection, $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    public function columns($columns)
    {
        $this->columns = $columns;
        return $this;
    }

    private function makeWhere($column, $operator, $value, $type)
    {
        return ['type' => $type, 'column' => $column, 'operator' => $operator, 'value' => $value, 'sub' => []];
    }

    public function where($column, $operator, $value, $type = null, $sub = false)
    {
        if ($sub) {
            $this->where[count($this->where) - 1]['sub'][] = $this->makeWhere($column, $operator, $value, $type);
        } else {
            $this->where[] = $this->makeWhere($column, $operator, $value, $type ?? 'where');
        }

        return $this;
    }

    public function or($column, $operator, $value, $sub = false)
    {
        return $this->where($column, $operator, $value, "or", $sub);
    }

    public function and($column, $operator, $value, $sub = false)
    {
        return $this->where($column, $operator, $value, "and", $sub);
    }

    public function orderBy($column)
    {
        $this->orderBy = $column;
        return $this;
    }

    public function groupBy($column)
    {
        $this->groupBy = $column;
        return $this;
    }

    public function limit($num)
    {
        $this->limit = $num;
        return $this;
    }

    public function offset($num)
    {
        $this->offset = $num;
        return $this;
    }

    public function join($table, $column, $fkColumn)
    {
        $this->join[] = [$table, $column, $fkColumn];
        return $this;
    }

    public function get($id = null)
    {
        if ($id != null) {
            $this->where('id', '=', $id);
        }

        return $this->exec();
    }

    public function insert($data)
    {
        $this->type = 'insert';
        $this->data = $data;

        return $this;
    }

    public function update($id, $data = null)
    {
        $this->type = 'update';
        $data = $data ?? $id;

        if (!is_array($id)) {
            $this->where('id', '=', $id);
        }

        $this->data = $data;
        return $this;
    }

    public function delete($id = null)
    {
        $this->type = 'delete';

        if ($id != null) {
            $this->where('id', '=', $id);
        }

        return $this;
    }

    public function raw($sql, $prepares)
    {
        $this->type = 'raw';
        $this->raw = $sql;
        $this->prepares = $prepares;
        return $this;
    }

    private function prepareData($data = null)
    {
        $data = $data ?? $this->data;

        foreach ($data as $value) {
            if (is_array($value)) {
                $this->prepareData($value);
            } else {
                $this->prepares[] = $value;
            }
        }

        return $this;
    }

    private function prepareWhere($data = null)
    {
        $data = $data ?? $this->where;

        foreach ($data as $where) {
            $this->prepares[] = $where['value'];

            if (!empty($where['sub'])) {
                $this->prepareWhere($where['sub']);
            }
        }

        return $this;
    }

    public function exec()
    {
        if ($this->type != 'raw') {
            $this->prepareData();
            $this->prepareWhere();
        }

        $query = new Query($this->connection, $this);
        if ($this->type == 'insert') {
            return $this->connection->get()->lastInsertId();
        }

        return $query;
    }
}
