<?php

/*
 * This file is part of the RsyFramework package
 *
 * (c) Dreamans <dreamans@rsycoder.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimble\Log;

use Nimble\Log\Exception\LoggerException;

class LoggerFile extends LoggerAbstract
{
    private $filePath = '';

    private $fileNameRule = '{type}.{year}{month}{day}.log';

    private $dateReplace = [];

    public function __construct($filePath, $fileNameRule = '')
    {
        $this->filePath = $filePath;
        if ($fileNameRule) {
            $this->fileNameRule = $fileNameRule;
        }
        $this->dateReplace = [
            'match' => [
                '{year}', '{month}', '{day}',
            ],
            'replace' => [
                date('Y'), date('m'), date('d'),
            ],
        ];

        $this->createPath();
    }

    protected function write($type, $message, array $context = [])
    {
        $data = "{$type} : {$message}";
        $content = sprintf("[%s] [%s] %s", date('Y-m-d H:i:s'), strtoupper($type), $message, json_encode($context));
        if ($context) {
            $content .= sprintf(' %s', json_encode($context));
        }
        $content .= "\n";
        $fileName = $this->createFileName($type);
        return $this->writeToFile($fileName, $content);
    }

    private function createPath()
    {
        if (!is_dir($this->filePath)) {
            if (!mkdir($this->filePath, 0755, true)) {
                throw LoggerException::logPathCreateFailed($this->filePath);
            }
        }
    }

    private function writeToFile($fileName, $content) 
    {
        if (false === file_put_contents($fileName, $content, FILE_APPEND)) {
            throw LoggerException::logFileWriteFailed($fileName);
        }
        return true;
    }

    private function createFileName($type)
    {
        $fileName = str_replace($this->dateReplace['match'], $this->dateReplace['replace'], $this->fileNameRule);
        return $this->filePath . '/' .str_replace('{type}', $type, $fileName);
    }
}
