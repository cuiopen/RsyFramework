<?php

/*
 * This file is part of the RsyFramework package
 *
 * (c) Dreamans <dreamans@rsycoder.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimble\Http\Exception;

class NotFoundException extends HttpException
{
    public function __construct($message)
    {
        parent::__construct(404, $message);
    }
}