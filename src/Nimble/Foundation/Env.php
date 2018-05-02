<?php

/*
 * This file is part of the RsyFramework package
 *
 * (c) Dreamans <dreamans@rsycoder.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimble\Foundation;

class Env 
{
    const NIMBLE_VERSION = '0.1.0';

    public static function nimbleVersion()
    {
        return self::NIMBLE_VERSION;
    }

    public static function phpVersion($isInt = false)
    {
        if ($isInt) {
            return PHP_VERSION_ID;
        }
        return phpversion();
    }
}