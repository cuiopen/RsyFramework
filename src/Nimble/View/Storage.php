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

class Storage
{
    private $path;

    private $cpath;

    public function __construct($path, $cpath)
    {
        $this->path = $path;
        $this->cpath = $cpath;
        $this->createPath($this->cpath);
    }

    public function sourceKey($tplName)
    {
        $srcFile = sprintf('%s/%s.view.php', $this->path, $tplName);
        return str_replace('/', DIRECTORY_SEPARATOR, $srcFile);
    }

    public function compileKey($tplName)
    {
        $dstFile = sprintf('%s/%s.php', $this->cpath, md5($tplName));
        return str_replace('/', DIRECTORY_SEPARATOR, $dstFile);
    }

    public function readContent($key)
    {
        if (!is_file($key)) {
            throw ViewException::sourceFileNotFound($key);
        }
        return file_get_contents($key);
    }

    public function keyMtime($key)
    {
        return is_file($key) ? filemtime($key) : 0;
    }

    public function writeContent($key, $content)
    {
        file_put_contents($key, $content);
    }

    private function createPath($path)
    {
        if (!is_dir($path)) {
            if (!@mkdir($path, 0755, true)) {
                throw ViewException::filePathCreateFailed($path);
            }
        }
    }
}