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

class Container
{
    private $variables = [];

    public function __construct(array $vars = [])
    {
        foreach($vars as $key => $val) {
            $this->variables[$key] = $val;
        }
    }

    public function __get($key)
    {
        return isset($this->variables[$key]) ? $this->variables[$key] : null;
    }

    public function __set($key, $value)
    {
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