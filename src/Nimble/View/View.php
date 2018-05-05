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

class View
{
    private $storage;

    public static function create($path, $cpath)
    {
        return new View(new Storage($path, $cpath));
    }

    private function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function display($tplName, array $vars = [])
    {
        $tplFile = Compile::build($this->storage, $tplName);
        extract($vars, EXTR_OVERWRITE);
        ob_start();
        include $tplFile;
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function __call($method, $arguments)
    {
        
    }
}