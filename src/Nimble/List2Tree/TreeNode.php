<?php

/*
 * This file is part of the RsyFramework package
 *
 * (c) Dreamans <dreamans@rsycoder.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimble\List2Tree;

class TreeNode
{
    private $parent;

    private $data;

    private $children = [];

    public function __construct(TreeNode $parent = null, DataNode $data = null)
    {
        if (null !== $parent) {
            $this->setParent($parent);
        }
        if (null !== $data) {
            $this->setData($data);
        }
    }

    public function setParent(TreeNode $parent)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setData(DataNode $data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function appendChildren(TreeNode $data, $key = null)
    {
        if (null !== $key) {
            $this->children[$key] = $data;
        } else {
            $this->children[] = $data;
        }
    }
    
    public function getChildren()
    {
        return $this->children;
    }
}
