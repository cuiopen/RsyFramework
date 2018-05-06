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

abstract class LoggerAbstract
{
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const INFO      = 'info';
    const SQL       = 'sql';
    const ACCESS    = 'access';

    public function error($message, array $context = [])
    {
        return $this->write(self::ERROR, $message, $context);
    }

    public function warning($message, array $context = [])
    {
        return $this->write(self::WARNING, $message, $context);
    }

    public function info($message, array $context = [])
    {
        return $this->write(self::INFO, $message, $context);
    }

    public function sql($message, array $context = [])
    {
        return $this->write(self::SQL, $message, $context);
    }

    public function access($message, array $context = [])
    {
        return $this->write(self::access, $message, $context);
    }

    abstract protected function write($type, $message, array $context = []);

}