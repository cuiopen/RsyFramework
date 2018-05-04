<?php

/*
 * This file is part of the RsyFramework package
 *
 * (c) Dreamans <dreamans@rsycoder.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimble\Http;

class Header
{
    private $headers = [];

    public function __construct(array $headers = [])
    {
        $this->sets($headers);
    }

    public function set($key, $value)
    {
        $key = $this->convertKey($key);
        $this->headers[$key] = $value;
    }

    public function sets(array $headers)
    {
        foreach ($headers as $key => $header) {
            $this->set($key, $header);
        }
    }

    public function get($key)
    {
        $key = $this->convertKey($key);
        return !isset($this->headers[$key]) ?: $this->headers[$key];
    }

    public function all()
    {
        if (!$this->headers) {
            return [];
        }
        $headers = [];
        foreach ($this->headers as $key => $val) {
            $headers[$this->convertOutputKey($key)] = $val;
        }
        return $headers;
    }

    public function remove($key)
    {
        $key = $this->convertKey($key);
        unset($this->headers[$key]);
    }

    public function has($key)
    {
        return array_key_exists($this->convertKey($key), $this->headers);
    }

    private function convertKey($key)
    {
        return str_replace('_', '-', strtolower($key));
    }

    private function convertOutputKey($key)
    {
        return ucwords($key, '-');
    }
}