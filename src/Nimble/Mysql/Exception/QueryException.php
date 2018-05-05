<?php

/*
 * This file is part of the RsyFramework package
 *
 * (c) Dreamans <dreamans@rsycoder.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimble\Mysql\Exception;

use RuntimeException;

class QueryException extends RuntimeException 
{
    public static function executeSql(array $errorInfo, $sql)
    {
        $message = sprintf('Mysql query error %s(%s): %s, SQL: [ %s ]', $errorInfo[1], $errorInfo[0], $errorInfo[2], $sql);
        return new static($message);
    }

    public static function transaction($message)
    {
        $message = sprintf("Mysql translation error: %s", $message);
        return new static($message);
    }
}