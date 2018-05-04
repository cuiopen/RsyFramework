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

use InvalidArgumentException;

class Container
{
    private $variables = [];

    private $readOnlyFields = [];

    public function __construct(array $vars = [])
    {
        foreach($vars as $key => $val) {
            if (is_array($val)) {
                if (count($val) != 2) {
                    continue;
                }
                list($val, $readOnly) = $val;
                $this->readOnlyFields[$key] = boolval($readOnly);
            }
            $this->variables[$key] = $val;
        }
    }

    public function __get($key)
    {
        return isset($this->variables[$key]) ? $this->variables[$key] : null;
    }

    public function __set($key, $value)
    {
        if (isset($this->readOnlyFields[$key]) && true === $this->readOnlyFields[$key]) {
            throw new InvalidArgumentException(sprintf('Container $key "%s" is read only', $key));
        }
        $this->variables[$key] = $value;
    }

    public function exists($key)
    {
        return isset($this->variables[$key]) ? true : false;
    }

    public function all()
    {
        return $this->variables;
    }

    public function flush()
    {
        $this->variables = [];
    }
}