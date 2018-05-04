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

use RuntimeException;

class Bootstrap 
{
    private $configure;

    private $bootConfig;

    private static $bootInstance = null;

    public static function bootstrap(array $bootConfig = [])
    {
        if (!self::$bootInstance) {
            self::$bootInstance = new Bootstrap($bootConfig);
        }
        return self::$bootInstance;
    }

    public function application($appClassName, $appConfig = 'app')
    {
        $this->configure->setConfig('app', $this->configure->getConfig($appConfig));

        return new $appClassName(self::$bootInstance);
    }

    public function __construct(array $config)
    {
        $this->checkRuntimeEnv();
        $this->bootInit($config);
    }

    public function configure($key)
    {
        return $this->configure->getConfig($key);
    }

    public function nimbleVersion()
    {
        return Env::nimbleVersion();
    }

    public function phpVersion($isInt = false)
    {
        return Env::phpVersion($isInt);
    }

    private function bootInit(array $config)
    {
        $this->bootConfig = $this->bootConfig($config);
        $this->configure = new Configure($this->bootConfig->configPath);

        $this->bindErrorHandle();
        $this->bootDefaultSetting();
    }

    private function bootConfig(array $config = [])
    {
        foreach ($config as $key => $val) {
            $newKey = preg_replace_callback('/([-_]+([a-z]{1}))/i', function($m){
                return strtoupper($m[2]);
            }, $key);
            unset($config[$key]);
            $config[$newKey] = $val;
        }

        $bootConfig = new Container($config);
        if (!$bootConfig->appPath) {
            $bootConfig->appPath = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'app';
        }
        if (!$bootConfig->configPath) {
            $bootConfig->configPath = $bootConfig->appPath .  DIRECTORY_SEPARATOR . 'config';
        }
        if (!$bootConfig->userExceptionHandle) {
            $bootConfig->userExceptionHandle = ExceptionHandle::class;
        }
        return $bootConfig;
    }

    private function checkRuntimeEnv()
    {
        if (Env::phpVersion(true) < 50600) {
            trigger_error('Nimble require PHP version >= 5.6.0', E_USER_ERROR);
        }
    }

    private function bindErrorHandle()
    {
        new Error($this->bootConfig->userExceptionHandle);
    }

    private function bootDefaultSetting()
    {
        error_reporting(E_ALL);
        if (defined('DEVELOP_DEBUG')) {
            ini_set('display_errors', 'on');
        } else {
            ini_set('display_errors', 'off');
        }
        date_default_timezone_set('Asia/Shanghai');
    }
}