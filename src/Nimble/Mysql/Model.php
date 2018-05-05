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

class Model
{
    private static $clientPool = [];

    protected $tbPrefix;

    protected $table;

    protected $client;

    protected $config;

    protected $clientName;

    protected $insertPair = [];

    public function __construct()
    {
        if (!$this->clientName) {
            $config = $this->getClientConfig();
            $this->clientName = substr(md5(implode(',', [$config['host'], $config['port'], $config['user']])), 0, 8);
        }

        if (!isset(self::$clientPool[$this->clientName])) {
            $config = $this->getClientConfig();
            self::$clientPool[$this->clientName] = new Client($config);
        }

        $this->client = self::$clientPool[$this->clientName];
    }

    public function save()
    {
        $model = $this->data($this->insertPair);
        return $model->save();
    }

    public static function __callStatic($method, $parameters)
    {
        $instance = new static;
        return call_user_func_array([$instance, $method], $parameters);
    }

    public function __call($method, $parameters)
    {
        $query = new Table(
            $this->client, 
            $this->table, 
            $this->tbPrefix
        );
        return call_user_func_array([$query, $method], $parameters);
    }

    public function __set($key, $val)
    {
        $this->insertPair[$key] = $val;
    }

    private function getClientConfig()
    {
        $cfg = [];
        foreach (['host', 'port', 'user', 'pass', 'db', 'charset', 'query_success_cb', 'query_error_cb'] as $key) {
            $cfg[$key] = isset($this->config[$key]) ? $this->config[$key] : null;
        }
        return $cfg;
    }
}