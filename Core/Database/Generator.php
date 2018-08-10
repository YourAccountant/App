<?php

namespace Core\Database;

class Generator
{

    public static function where($data)
    {
        $count = 0;
        $sql = "";
        foreach ($data as $where) {
            if ($count == 0) {
                $sql .= "WHERE ";
            } else {
                $sql .= " " . strtoupper($where['type']) . " ";
            }

            if (!empty($where['sub'])) {
                $sql .= " ( ";
                $sql .= " `{$where['column']}` {$where['operator']} ? ";

                foreach ($where['sub'] as $sub) {
                    $sql .= " ".strtoupper($sub['type']) . " `{$sub['column']}` {$sub['operator']} ? ";
                }

                $sql .= ")";
            } else {
                $sql .= " `{$where['column']}` {$where['operator']} ? ";
            }
            ++$count;
        }

        return $sql;
    }

    public static function join($table, $data)
    {
        if ($data == null) {
            return "";
        }

        $sql = "";
        foreach ($data as $join) {
            list($fkTable, $column, $fkColumn) = $join;
            $sql .= " INNER JOIN {$fkTable}ON `{$table}`.`$column` = `{$fkTable}`.`{$column}` ";
        }

        return $sql;
    }

    public static function orderBy($column)
    {
        if ($column == null) {
            return "";
        }

        return " ORDER BY {$column} ";
    }

    public static function groupBy($column)
    {
        if ($column == null) {
            return "";
        }

        return " GROUP BY {$column} ";
    }

    public static function limit($num)
    {
        if ($num == null) {
            return "";
        }

        return " LIMIT {$num} ";
    }

    public static function offset($num)
    {
        if ($num == null) {
            return "";
        }

        return " OFFSET {$num} ";
    }

    public static function select($table, $select = "*")
    {
        return " SELECT {$select} FROM `{$table}` ";
    }

    public static function delete($table)
    {
        return " DELETE FROM `{$table}` ";
    }

    public static function insert($table)
    {
        return " INSERT INTO `{$table}` ";
    }

    public static function update($table)
    {
        return " UPDATE `{$table}` ";
    }

    public static function insertRows($data)
    {
        $multirow = isset($data[0]) && is_array($data[0]);
        $sql = "";
        $columns = $multirow ? array_keys($data[0]) : array_keys($data);

        $sql .= " ( ";
        foreach ($columns as $column) {
            $sql .= " `{$column}`, ";
        }
        $sql = rtrim(trim($sql), ',');
        $sql .= " ) ";

        $sql .= " VALUES ";

        if (!$multirow) {
            $values = array_values($data);
            $sql .= " ( ";
            foreach ($values as $columns) {
                $sql .= " ?, ";
            }
            $sql = rtrim(trim($sql), ',');
            $sql .= " ) ";
        } else {
            foreach ($data as $row) {
                $sql .= " ( ";
                foreach ($row as $column) {
                    $sql .= " ?, ";
                }
                $sql = rtrim(trim($sql), ',');
                $sql .= " ), ";
            }
            $sql = rtrim(trim($sql), ',');
        }

        return $sql;
    }

    public static function updateRows($data)
    {
        $sql = " SET ";
        foreach ($data as $key => $value) {
            $sql .= " `$key` = ?, ";
        }
        $sql = rtrim(trim($sql), ',') . " ";

        return $sql;
    }
}
