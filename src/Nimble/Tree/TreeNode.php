<?php

/*
 * This file is part of the RsyFramework package
 *
 * (c) Dreamans <dreamans@rsycoder.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimble\Tree;

class TreeNode
{
    public function __construct(array $data = [], $childrenKey = 'children')
    {
        foreach ($data as $key => $val) {
            $this->{$key} = $val;
        }
        $this->{$childrenKey} = [];
    }
}
