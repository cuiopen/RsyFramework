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

use Pdo;
use Closure;
use StdClass;
use Nimble\Mysql\Exception\QueryException;

class Client
{
    private $pdoLink = null;

    private $transaction = null;

    private $config = [];

    private $lastQuerySql = [];

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->pdoLink = $this->connect();
    }

    public function table($tbName, $tbPrefix = '')
    {
        return new Table($this, $tbName, $tbPrefix);
    }

    public function lastQuerySql()
    {
        return $this->lastQuerySql;
    }

    public function lastQuerySqlString()
    {
        return json_encode($this->lastQuerySql);
    }

    public function beginTransaction()
    {
        if ($this->transaction) {
            throw QueryException::transaction("translation is active and cannot open new translation operations");
        }
        return $this->transaction = $this->pdoLink->beginTransaction();
    }

    public function commit()
    {
        if (!$this->transaction) {
            throw QueryException::transaction("translation is inactive and cannot perform commit operations");
        }
        $this->transaction = false;
        return $this->pdoLink->commit();
    }

    public function rollBack()
    {
        if (!$this->transaction) {
            throw QueryException::transaction("translation is inactive and cannot perform roll back operations");
        }
        $this->transaction = false;
        return $this->pdoLink->rollBack();
    }

    public function select($sql, array $prepare = [])
    {
        $pdoSh = $this->query($sql, $prepare);
        return $pdoSh->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($sql, array $prepare = [])
    {
        $this->query($sql, $prepare);
        return $this->pdoLink->lastInsertId();
    }

    public function update($sql, array $prepare = [])
    {
        $pdoSh = $this->query($sql, $prepare);
        return $pdoSh->rowCount();
    }

    public function lastInsertId()
    {
        return $this->pdoLink->lastInsertId();
    }

    public function query($sql, array $prepare = [])
    {
        list($execStartUsTime, $execStartSecTime) = explode(' ', microtime());
        $pdoSth = $this->pdoLink->prepare($sql);
        $pdoSth->execute($prepare);
        $this->lastQuerySql = [$pdoSth->queryString, $prepare];
        $errorInfo = $pdoSth->errorInfo();
        if ($errorInfo[0] != '00000') {
            $this->queryErrorCb($errorInfo, $sql, $prepare);
            throw QueryException::executeSql($errorInfo, $sql, $errorInfo[1]);
        }
        list($execFinishUsTime, $execFinishSecTime) = explode(' ', microtime());
        $execUsTime = round((($execFinishUsTime + $execFinishSecTime) - ($execStartUsTime + $execStartSecTime)) * 1000, 3);

        $this->querySuccessCb($sql, $execUsTime, $prepare);
        return $pdoSth;
    }

    private function connect()
    {
        $dsn = $this->getDsn();
        $user = $this->getUser();
        $pass = $this->getPass();
        $options = $this->getOptions();

        return new Pdo($dsn, $user, $pass, $options);
    }

    private function getDsn()
    {
        $dsn = [];
        if (isset($this->config['db'])) {
            $dsn[] = "dbname={$this->config['db']}";
        }
        if (isset($this->config['host'])) {
            $dsn[] = "host={$this->config['host']}";
        }
        if (isset($this->config['port'])) {
            $dsn[] = "port={$this->config['port']}";
        }
        if (isset($this->config['charset'])) {
            $dsn[] = "charset={$this->config['charset']}";
        }
        $strDsn = "mysql:" . implode(';', $dsn);

        return $strDsn;
    }

    private function getOptions()
    {
        return isset($this->config['options']) ? $this->config['options'] : [];
    }

    private function getUser()
    {
        return isset($this->config['user']) ? $this->config['user'] : null;
    }
    
    private function getPass()
    {
        return isset($this->config['pass']) ? $this->config['pass'] : null;
    }

    private function queryErrorCb($errorInfo, $sql, array $prepare = [])
    {
        $paramObj = new StdClass();
        $paramObj->errorInfo = $errorInfo;
        $paramObj->sql = $sql;
        $paramObj->prepare = $prepare;

        $this->callbackHook('query_error_cb', $paramObj);
    }

    private function querySuccessCb($sql, $execUsTime, array $prepare = []) 
    {
        $paramObj = new StdClass();
        $paramObj->sql = $sql;
        $paramObj->prepare = $prepare;
        $paramObj->execTime = $execUsTime;

        $this->callbackHook('query_success_cb', $paramObj);
    }

    private function callbackHook($cbName, StdClass $paramObj) {
        $cb = isset($this->config[$cbName]) ? $this->config[$cbName]: null;
        if (
            (is_object($cb) && ($cb instanceof Closure))
            || (is_string($cb) && function_exists($cb))
        ) {
            $cb($paramObj);
        }
    }
}
