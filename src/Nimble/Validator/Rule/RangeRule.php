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

class RangeRule implements RuleInterface
{
    public static function rule($value, $param)
    {
        $value   = floatval($value);
        $range   = explode(',', $param);
        $minRule = isset($range[0]) ? floatval($range[0]) : PHP_INT_MAX * (-1);
        $maxRule = isset($range[1]) ? floatval($range[1]) : PHP_INT_MAX;
        return ($value >= $minRule) && ($value <= $maxRule);
    }
}