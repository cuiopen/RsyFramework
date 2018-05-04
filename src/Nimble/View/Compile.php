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

use Nimble\View\Exception\ViewException;

class Compile
{
    protected $content;

    public static function build($sourceFile)
    {
        $compile = new Compile($sourceFile);
    }

    private function __construct($sourceFile)
    {
        $this->content = $this->getContent($sourceFile);
    }

    private function getContent()
    {
        if (!is_file($this->sourceFile)) {
            throw ViewException::sourceFileNotFound($this->sourceFile);
        }
        return file_get_contents($this->sourceFile);
    }
}