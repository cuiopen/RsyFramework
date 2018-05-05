<?php

/*
 * This file is part of the RsyFramework package
 *
 * (c) Dreamans <dreamans@rsycoder.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimble\View;

use Nimble\Exception\ViewException;

class Compile
{
    protected $sourceKey;

    protected $compileKey;

    protected $storage;

    protected $content;

    public static function build(Storage $storage, $tplName)
    {
        $compile = new Compile($storage, $tplName);
        $compile->checkNeedBuild()
        && $compile->readContent()
                   ->compileTpl()
                   ->writeContent();

        return $compile->compileKey;
    }

    private function __construct(Storage $storage, $tplName)
    {
        $this->storage = $storage;
        $this->sourceKey = $this->storage->sourceKey($tplName);
        $this->compileKey = $this->storage->compileKey($tplName);
    }

    private function checkNeedBuild()
    {
        $smtime = $this->storage->keyMtime($this->sourceKey);
        $cmtime = $this->storage->keyMtime($this->compileKey);
        return (!$cmtime || $smtime >= $cmtime) ? true : false;
    }

    private function readContent()
    {
        $this->content = $this->storage->readContent($this->sourceKey);
        return $this;
    }

    private function writeContent()
    {
        $this->storage->writeContent($this->compileKey, $this->content);
        return $this;
    }

    private function compileTpl()
    {
        if (!$this->content) {
            return $this;
        }

        $this->content = CompileSyntax::compileTags($this->content);

        return $this;
    }
}