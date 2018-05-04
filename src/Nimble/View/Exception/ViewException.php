<?php

namespace Nimble\View\Exception;

use RuntimeException;

class ViewException extends RuntimeException 
{
    public static function sourceFileNotFound($sourceFile)
    {
        $message = sprintf('View template file "%s" not found', $sourceFile);
        return new static($message);
    }
}