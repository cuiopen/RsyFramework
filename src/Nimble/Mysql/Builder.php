<?php

/*
 * This file is part of the RsyFramework package
 *
 * (c) Dreamans <dreamans@rsycoder.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimble\Mysql;

class Builder 
{
    const LIMIT_DEFAULT = '0, 999';

    private $prefix = '';

    private $table;

    private $wheres = [];

    private $prepares = [];

    private $columns = [];

    private $orders = [];

    private $groups = [];

    private $limit;

    private $data = [];

    public function __construct($prefix = '', $tbName = '')
    {
        $this->prefix = $prefix;
        $this->table = $tbName;
    }

    public function selectSql()
    {
        $where = $this->toWheres();
        $where = $where ? ' AND ' . $where: ' ';
        $sql = sprintf("SELECT %s FROM %s WHERE 1%s%s%s LIMIT %s", 
            $this->toColumns(), $this->toTable(), $where, 
            $this->toGroups(), $this->toOrders(), $this->toLimit()
        );
        return $sql;
    }

    public function countSql()
    {
        $where = $this->toWheres();
        $where = $where ? ' AND ' . $where: '';
        $sql = sprintf("SELECT COUNT(1) as count FROM %s WHERE 1%s", 
            $this->toTable(), $where
        );
        return $sql;
    }

    public function insertSql()
    {
        list($fields, $values) = $this->toData();
        $sql = sprintf("INSERT INTO %s(%s) VALUES(%s)",
            $this->toTable(), $fields, $values
        );
        return $sql;
    }

    public function updateSql()
    {
        $sql = sprintf("UPDATE %s SET %s WHERE %s",
            $this->toTable(), $this->toUpdate(), $this->toWheres()
        );
        return $sql;
    }

    public function deleteSql()
    {
        $sql = sprintf("DELETE FROM %s WHERE %s",
            $this->toTable(), $this->toWheres()
        );
        return $sql;
    }

    public function prepares()
    {
        return $this->prepares;
    }

    public function setTable($tbName, $tbPrefix = null)
    {
        $this->table = $tbName;
        if (null !== $tbPrefix) {
            $this->prefix = $tbPrefix;
        }
    }

    public function setLimit($offset = 0, $limit = 999)
    {
        $this->limit = sprintf("%d, %d", intval($offset), intval($limit));
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function addOrderBy($column, $direction = 'asc')
    {
        $this->orders[] = [$column, $direction];
    }

    public function addGroupBy($column)
    {
        $this->groups[] = $column;
    }

    public function addArrayWhere($column, $tag = null, $value = null)
    {
        $this->wheres[] = [$column, $tag, $value];
    }

    public function addArrayPrepare($key, $value)
    {
        $this->prepares[$key] = $value;
    }

    public function addArrayColumns($colums, $as = '', $tag = '`')
    {
        $this->columns[] = [$colums, $as, $tag];
    }

    private function toOrders()
    {
        $order = "";
        if ($this->orders) {
            $arrOrder = [];
            foreach ($this->orders as $odr) {
                list($col, $direction) = $odr;
                $col = trim($col);
                $direction = strtoupper($direction);
                $arrOrder[] = "`{$col}` {$direction}";
            }
            $order = " ORDER BY " . implode(', ', $arrOrder);
        }
        return $order;
    }

    private function toGroups()
    {
        $group = "";
        if ($this->groups) {
            $arrGroup = [];
            foreach ($this->groups as $gop) {
                $gop = trim($gop);
                $arrGroup[] = "`{$gop}`";
            }
            $group = " GROUP BY ". implode(',', $arrGroup);
        }
        return $group;
    }

    private function toTable()
    {
        return "`{$this->prefix}{$this->table}`";
    }

    private function toColumns()
    {
        $columns = "*";
        if ($this->columns) {
            $arrCols = [];
            foreach ($this->columns as $column) {
                list($col, $as, $tag) = $column;
                $col = trim($col);
                $as  = trim($as);
                if ($as) {
                    $arrCols[] = "{$tag}".trim($col)."{$tag} as {$tag}{$as}{$tag}";
                } else {
                    $arrCols[] = "{$tag}".trim($col)."{$tag}";
                }
            }
            $columns = implode(', ', $arrCols);
        }
        return $columns;
    }

    private function toWheres()
    {
        $arrWhere = [];
        if ($this->wheres) {
            foreach ($this->wheres as $w) {
                list($col, $bool, $val) = $w;
                if ($bool) {
                    $arrWhere[] = "`{$col}` {$bool} {$val}";
                } else {
                    $arrWhere[] = $col;
                }
            }
        }
        $where = implode (' AND ', $arrWhere);
        return $where;
    }

    private function toData()
    {
        $insData = $this->data;
        $fields = $values = [];
        foreach ($insData as $key => $val) {
            $fields[] = "`{$key}`";
            $values[] = $val;
        }
        return [implode(", ", $fields), implode(", ", $values)];
    }

    private function toLimit()
    {
        $limit = $this->limit;
        if (!$limit) {
            $limit = self::LIMIT_DEFAULT;
        }
        return $limit;
    }

    private function toUpdate()
    {
        $update = [];
        $upData = $this->data;
        foreach ($upData as $field => $val) {
            $update[] = sprintf("`%s` = %s", $field, $val);
        }
        return implode(", ", $update);
    }
}
