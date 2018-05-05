<?php

/*
 * This file is part of the RsyFramework package
 *
 * (c) Dreamans <dreamans@rsycoder.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimble\Validator\Exception;

use RuntimeException;

class ValidatorException extends RuntimeException
{
    public static function invalidArgument($message)
    {
        return new static($message);
    }

    public static function badMethodCall($message)
    {
        return new static($message);
    }
}

