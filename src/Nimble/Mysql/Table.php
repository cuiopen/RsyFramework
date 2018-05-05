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

class Table 
{
    private $client;

    private $builder;

    public function __construct($client, $tbName, $tbPrefix = '')
    {
        $this->client = $client;
        $this->builder = new Builder($tbPrefix, $tbName);
    }

    public function client()
    {
        return $this->client;
    }

    public function instance()
    {
        return $this;
    }

    public function column()
    {
        $columns = func_get_args();
        if ($columns) {
            foreach ($columns as $col) {
                if (is_numeric($col)) {
                    continue;
                }
                $this->builder->addArrayColumns($col);
            }
        }
        return $this;
    }

    public function data(array $data)
    {
        $insData = [];
        foreach ($data as $field => $value) {
            if (is_numeric($field)) {
                continue;
            }
            $prepare = $this->makePrepare($field, $value);
            $insData[$field] = $prepare;
            $this->builder->addArrayPrepare($prepare, $value);
        }
        $this->builder->setData($insData);
        return $this;
    }

    public function columnAs($column, $as, $tag = '')
    {
        $this->builder->addArrayColumns($column, $as, $tag);
        return $this;
    }

    public function where()
    {
        $count = func_num_args();
        $args = func_get_args();

        if ($count && is_array($args[0])) {
            foreach ($args[0] as $column => $value) {
                $prepare = $this->makePrepare($column, $value);
                $this->builder->addArrayWhere($column, '=', $prepare);
                $this->builder->addArrayPrepare($prepare, $value);
            }
        } elseif ($count >= 2 && is_string($args[0])) {
            $column = $args[0];
            $value = $args[1];
            $tag = isset($args[2]) ? $args[2] : '=';
            $prepare = $this->makePrepare($column, $value);
            $this->builder->addArrayWhere($column, $tag, $prepare);
            $this->builder->addArrayPrepare($prepare, $value);
        }
        return $this;
    }

    public function whereRaw($condition, array $prepare = [])
    {
        $this->builder->addArrayWhere($condition);
        
        if ($prepare) {
            foreach ($prepare as $key => $val) {
                $this->builder->addArrayPrepare($key, $val);
            }
        }
        return $this;
    }

    public function whereIn($column, array $arrValue)
    {
        if (!$arrValue) {
            return $this;
        }
        $arrPrepare = [];
        foreach ($arrValue as $key => $val) {
            $arrPrepare[] = $prepare = $this->makePrepare($column.$key, $val);
            $this->builder->addArrayPrepare($prepare, $val);
        }
        $value = '('. implode(',', $arrPrepare) .')';
        $this->builder->addArrayWhere($column, 'in', $value);
        return $this;
    }

    public function orderBy($column, $direction = 'asc')
    {
        $this->builder->addOrderBy($column, $direction);
        return $this;
    }

    public function groupBy($column)
    {
        $this->builder->addGroupBy($column);
        return $this;
    }

    public function limit($offset = 0, $limit = 999)
    {
        $this->builder->setLimit($offset, $limit);
        return $this;
    }

    public function all()
    {
        $sql = $this->builder->selectSql();
        return $this->client->select($sql, $this->builder->prepares());
    }

    public function first()
    {
        $this->limit(0, 1);
        $arrResult = $this->all();
        if (!isset($arrResult[0])) {
            return [];
        }
        return $arrResult[0];
    }

    public function save()
    {
        $sql = $this->builder->insertSql();
        return $this->client->insert($sql, $this->builder->prepares());
    }

    public function update()
    {
        $sql = $this->builder->updateSql();
        return $this->client->update($sql, $this->builder->prepares());
    }

    public function delete()
    {
        $sql = $this->builder->deleteSql();
        return $this->client->update($sql, $this->builder->prepares());
    }

    public function count()
    {
        $sql = $this->builder->countSql();
        $ret = $this->client->select($sql, $this->builder->prepares());
        if (isset($ret[0]['count'])) {
            return intval($ret[0]['count']);
        }
        return 0;
    }

    public function exists() 
    {
        if ($this->count()) {
            return true;
        }
        return false;
    }

    public function lastInsertId()
    {
        return $this->client->lastInsertId();
    }

    private function makePrepare($key, $entropy)
    {
        $rawKey = md5($key. $entropy . rand());
        return ':' . $key . '_' . substr($rawKey, rand(0, 24), 8);
    }
}