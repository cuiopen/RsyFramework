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

class LengthRule implements RuleInterface
{
    public static function rule($value, $param)
    {
        $value   = mb_strlen($value);
        $range   = explode(',', $param);
        $minRule = isset($range[0]) ? floatval($range[0]) : 0;
        $maxRule = isset($range[1]) ? floatval($range[1]) : PHP_INT_MAX;
        return ($value >= $minRule) && ($value <= $maxRule);
    }
}