<?php

/*
 * This file is part of the RsyFramework package
 *
 * (c) Dreamans <dreamans@rsycoder.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimble\Foundation;

use ErrorException;
use Exception;
use Error as BaseError;

class Error 
{
    private $userHandle;

    public function __construct($userHandle)
    {
        $this->userHandle = $userHandle;
        $this->handle();
    }

    private function handle()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 'off');
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    public function handleException($e)
    {
        if ($e instanceof BaseError) {
            throw new ErrorException($e->getMessage(), $e->getCode(), E_ERROR, $e->getFile(), $e->getLine(), $e->getPrevious());
        }
        $handle = new $this->userHandle($e);
        if ($handle instanceof ExceptionHandle) {
            $handle->render();
        }
    }

    public function handleShutdown()
    {
        if (!is_null($error = error_get_last()) && $this->isFatal($error['type'])) {
            $this->handleException(new ErrorException($error['message'], $error['type'], E_ERROR, $error['file'], $error['line']));
        }
    }

    private function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }
}