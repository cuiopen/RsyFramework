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

use Nimble\Foundation\Bootstrap;

class Application
{
    private $boot;

    public $response;

    public $request;

    private $appIsStarted;

    private $configure;

    public function __construct(Bootstrap $boot)
    {
        $this->boot = $boot;
    }

    public function bootstrap()
    {
        return $this->boot;
    }

    public function start()
    {
        if (true === $this->appIsStarted) {
            return $this;
        }
        $this->appIsStarted = true;
        $this->initApplication();
        $this->startWebApplication();
        return $this;
    }

    public function configure($key)
    {
        return $this->boot->configure($key);
    }

    private function initApplication()
    {
        $this->request = new Request();
        $this->response = new Response();
    }

    private function startWebApplication()
    {
        $controller = $this->buildRouter();
        $pre = $this->configure('app.controller.pre');
        if ($pre) {
            $controller = "{$pre}{$controller}";
        }
        Controller::build($this, $controller)->run();
        $this->response->send();
    }

    public function terminate()
    {
        $this->terminateWebApplication();
    }

    private function terminateWebApplication()
    {
        exit;
    }

    private function buildRouter()
    {
        $routerMap = $this->configure('app.router.map');
        $routerCallback = $this->configure('app.router.callback');
        return Router::build($this, $routerMap, $routerCallback);
    }
}