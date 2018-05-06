<?php

/*
 * This file is part of the RsyFramework package
 *
 * (c) Dreamans <dreamans@rsycoder.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimble\Log\Exception;

use RuntimeException;

class LoggerException extends RuntimeException 
{
    public static function logPathCreateFailed($path)
    {
        $message = sprintf('Log path "%s" create failed');
        return new static($message);
    }

    public static function logFileWriteFailed($fileName)
    {
        $message = sprintf('Log file "%s" write failed', $fileName);
        return new static($message);
    }

    public static function instanceInvalid()
    {
        return new static('LoggerFile class is not instantiated');
    }
}