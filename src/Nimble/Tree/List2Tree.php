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

class List2Tree
{
    private $tree = null;

    private $rootId;

    private $parentKey;

    private $currentKey;

    private $childrenKey;

    private $taskList = [];

    private $rawData = [];

    private function __construct(array $data, $rootId = 0, $parentKey = 'parent_id', $currentKey = 'id', $childrenKey = 'children')
    {
        $this->tree = $this->createEmptyNode();
        $this->rootId = $rootId;
        $this->parentKey = $parentKey;
        $this->currentKey = $currentKey;
        $this->childrenKey = $childrenKey;

        $this->rawData = $data;
    }

    public static function build(array $data, $rootId = 0, $parentKey = 'parent_id', $currentKey = 'id', $childrenKey = 'children')
    {
        $instance = new List2Tree($data);
        $instance->createRootNode();
        $instance->createTreeNode();

        return $instance->tree->{$childrenKey};
    }

    private function createTreeNode()
    {
        while (true) {
            $node = $this->pushTask();
            if (!$node) {
                break;
            }
            foreach ($this->rawData as $key => $item) {
                if ($item[$this->parentKey] == $node->{$this->currentKey}) {
                    $subNode = new TreeNode($item, $this->childrenKey);
                    $node->{$this->childrenKey}[] = $subNode;
                    unset($this->rawData[$key]);
                    $this->appendTask($subNode);
                }
            }
        }
    }

    private function createRootNode()
    {
        foreach ($this->rawData as $key => $item) {
            if ($item[$this->parentKey] == $this->rootId) {
                $node = new TreeNode($item, $this->childrenKey);
                $this->tree->{$this->childrenKey}[] = $node;
                $this->appendTask($node);
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
