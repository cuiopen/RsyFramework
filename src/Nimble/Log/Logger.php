<?php

/*
 * This file is part of the RsyFramework package
 *
 * (c) Dreamans <dreamans@rsycoder.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimble\Log;

use Nimble\Log\Exception\LoggerException;

class Logger
{
    private static $globalInstance = null;

    private $loggerInstance = null;
    
    public function __construct(array $logConfig)
    {
        $this->loggerInstance = new LoggerFile($logConfig['path'], $logConfig['file_name_rule']);
    }

    public static function globalInstance(array $logConfig)
    {
        if (!self::$globalInstance) {
            self::$globalInstance = new Logger($logConfig);
        }
        return self::$globalInstance;
    }

    public static function __callStatic($method, $parameters)
    {
        if (!self::$loggerInstance) {
            throw LoggerException::instanceInvalid();
        }
        return call_user_func_array([self::$loggerInstance, $method], $parameters);
    }

    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->loggerInstance, $method], $parameters);
    }
}