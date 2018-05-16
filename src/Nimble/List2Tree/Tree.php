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

class Tree 
{
    private $tree = null;

    private $rootId;

    private $parentKey;

    private $currentKey;

    private $taskList = [];

    private $rawData = [];

    private function __construct(array $data, $rootId = 0, $parentKey = 'parent_id', $currentKey = 'id')
    {
        $this->tree = $this->createEmptyNode();
        $this->rootId = $rootId;
        $this->parentKey = $parentKey;
        $this->currentKey = $currentKey;
        $this->rawData = $data;
    }

    public static function build(array $data, $rootId = 0, $parentKey = 'parent_id', $currentKey = 'id')
    {
        $instance = new List2Tree($data);
        $instance->createRootNode();
        $instance->createTreeNode();

        return $instance->tree;
    }

    private function createTreeNode()
    {
        while (true) {
            $node = $this->pushTask();
            if (!$node) {
                break;
            }
            foreach ($this->rawData as $key => $item) {
                if ($item[$this->parentKey] == $node->getData()->{$this->currentKey}) {
                    $subNode = new TreeNode($node, new DataNode($item));
                    $node->appendChildren($subNode, $item[$this->currentKey]);
                    unset($this->rawData[$key]);
                    $this->appendTask($subNode);
                }
            }
        }
    }

    private function createRootNode()
    {
        foreach ($this->rawData as $key => $item) {
            if ($item[$this->parentKey] == 0) {
                $node = new TreeNode($this->tree, new DataNode($item));
                $this->appendTask($node);
                $this->tree->appendChildren($node, $item[$this->currentKey]);
                unset($this->rawData[$key]);
            }
        }
    }

    private function appendTask(TreeNode $node)
    {
        array_push($this->taskList, $node);
    }

    private function pushTask()
    {
        return array_shift($this->taskList);
    }

    private function createEmptyNode()
    {
        return new TreeNode();
    }
}
