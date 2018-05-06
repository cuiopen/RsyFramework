<?php

/*
 * This file is part of the RsyFramework package
 *
 * (c) Dreamans <dreamans@rsycoder.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimble\Http;

use InvalidArgumentException;

class Response
{
    const HTTP_OK = 200;
    const HTTP_MULTIPLE_CHOICES = 300;
    const HTTP_MOVED_PERMANENTLY = 301;
    const HTTP_FOUND = 302;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;
    const HTTP_BAD_GATEWAY = 502;
    const HTTP_SERVICE_UNAVAILABLE = 503;
    const HTTP_GATEWAY_TIMEOUT = 504;

    public static $statusTexts = [
        self::HTTP_OK => 'OK',
        self::HTTP_MULTIPLE_CHOICES => 'Multiple Choices',
        self::HTTP_MOVED_PERMANENTLY => 'Moved Permanently',
        self::HTTP_FOUND => 'Found',
        self::HTTP_BAD_REQUEST => 'Bad Request',
        self::HTTP_UNAUTHORIZED => 'Unauthorized',
        self::HTTP_NOT_FOUND => 'Not Found',
        self::HTTP_METHOD_NOT_ALLOWED => 'Method Not Allowed',
        self::HTTP_INTERNAL_SERVER_ERROR => 'Internal Server Error',
        self::HTTP_NOT_IMPLEMENTED => 'Not Implemented',
        self::HTTP_BAD_GATEWAY => 'Bad Gateway',
        self::HTTP_SERVICE_UNAVAILABLE => 'Service Unavailable',
        self::HTTP_GATEWAY_TIMEOUT => 'Gateway Timeout',
    ];

    private $content;

    private $statusCode;

    private $statusText;

    private $header;

    private $version;

    private $charset;

    private $contentType;

    public function __construct()
    {
        $this->header = new Header();
        $this->setProtocolVersion('1.1');
    }

    public function create($content = '', $status = 200, $charset = 'utf-8', array $headers = [])
    {
        $this->setContent($content);
        $this->setStatusCode($status);
        $this->setCharset($charset);

        $this->header->sets($headers);
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    public function setHeader($key, $val)
    {
        $this->header->set($key, $val);
    }

    public function setStatusCode($code, $text = null)
    {
        if ($code < 100 || $code >= 600) {
            throw new InvalidArgumentException(sprintf('HTTP status code "%s" is not valid.', $code));
        }
        $this->statusCode = $code;
        if (null === $text) {
            $this->statusText = isset(self::$statusTexts[$code]) ? self::$statusTexts[$code] : 'unknown status';
        } else {
            $this->statusText = $text;
        }
    }

    public function setProtocolVersion($version)
    {
        $this->version = $version;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
    }

    private function sendHeaders()
    {
        if (headers_sent()) {
            return;
        }

        if ($this->contentType || $this->charset) {
            $ct = $this->contentType . ($this->charset ? '; charset=' . $this->charset : '');
            $this->header->set('content-type', $ct);
        }

        header(sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText), true, $this->statusCode);

        foreach($this->header->all() as $key => $value) {
            $header = "{$key}: {$value}";
            header($header, true, $this->statusCode);
        }        
    }

    private function sendContent()
    {
        echo $this->content;
    }
}