<?php

/*
 * This file is part of the RsyFramework package
 *
 * (c) Dreamans <dreamans@rsycoder.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimble\View\Exception;

use RuntimeException;

class ViewException extends RuntimeException 
{
    public static function sourceFileNotFound($sourceFile)
    {
        $message = sprintf('View template file "%s" not found', $sourceFile);
        return new static($message);
    }

    public static function filePathCreateFailed($path)
    {
        $message = sprintf('View template directory "%s" create failed', $path);
        return new static($message);
    }
}