<?php

/*
 * This file is part of the RsyFramework package
 *
 * (c) Dreamans <dreamans@rsycoder.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimble\Validator\Rule;

class RequireRule implements RuleInterface
{
    public static function rule($value, $param)
    {
        return !empty($value);
    }
}