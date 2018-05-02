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

use InvalidArgumentException;
use RuntimeException;

class Configure
{
    private $configPath;

    private $configValue = [];

    private $configKeyValue = [];

    public function __construct($configPath)
    {
        $this->configPath = $configPath;
    }

    public function getConfig($key)
    {
        if (!$key) {
            throw new InvalidArgumentException('Invalid config key');
        }
        if (!isset($this->configKeyValue[$key])) {
            $this->configKeyValue[$key] = $this->getConfigKeyValue($key);
        }
        return $this->configKeyValue[$key];
    }

    public function setConfig($fileKey, array $arrKeyValue)
    {
        if (!isset($this->configValue[$fileKey])) {
            return $this->configValue[$fileKey] = $arrKeyValue;
        }
        $this->configValue[$fileKey] = array_merge($this->configValue[$fileKey], $arrKeyValue);
    }

    private function getConfigKeyValue($key)
    {
        $fileCfgKey = $this->formatConfigKey($key);
        $arrKeyValue = $this->loadConfigFileValue($fileCfgKey['file_name']);
        if (!$fileCfgKey['key']) {
            return $arrKeyValue;
        }

        $tmpValue = $arrKeyValue;
        foreach($fileCfgKey['key'] as $k) {
            if (!isset($tmpValue[$k])) {
                return null;
            }
            $tmpValue = $tmpValue[$k];
        }
        return $tmpValue;
    }

    private function loadConfigFileValue($fileName)
    {
        if (isset($this->configValue[$fileName])) {
            return $this->configValue[$fileName];
        }
        $file = $this->configPath . DIRECTORY_SEPARATOR . $fileName . '.php';
        if (!is_file($file)) {
            throw new RuntimeException(sprintf('Config file "%s" not exists', $file));
        }
        $this->configValue[$fileName] = include $file;
        return $this->configValue[$fileName];
    }

    private function formatConfigKey($key)
    {
        $arrKey = array_filter(explode('.', $key));
        $fileName = array_shift($arrKey);
        return [
            'file_name' => $fileName,
            'key' => $arrKey,
        ];
    }
}