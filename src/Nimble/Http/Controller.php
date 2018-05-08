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

use Nimble\Foundation\Container;
use Nimble\Http\Exception\HttpRuntimeException;

class Controller
{
    private $container;

    private $terminate = false;

    public static function build(Application $app, $controller)
    {
        $arrContainer = [
            'app' => [$app, true],
            'controller' => [$controller, true],
            'request' => [$app->request, true],
            'response' => [$app->response, true],
            'content' => '',
        ];
        return new Controller(new Container($arrContainer));
    }

    private function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run()
    {
        if (!class_exists($this->container->controller)) {
            throw new HttpRuntimeException(sprintf('Controller class "%s" not exists', $this->container->controller));
        }
        $this->controller()->afterController();
        return $this->container;
    }

    private function controller()
    {
        if (true === $this->terminate) {
            return $this;
        }
        $ctlObject = new $this->container->controller($this->container);

        if (method_exists($ctlObject, 'response')) {
            $this->container->content = call_user_func([$ctlObject, 'response']);
        }

        $this->terminate = true;

        return $this;
    }

    private function afterController()
    {
        $defaultHeaders = [
            'X-Powered-By'  => 'RsyFramework',
            'Cache-control' => 'private',
        ];
        $statusCode = $this->container->statusCode ? 
            $this->container->statusCode : Response::HTTP_OK;

        $charset = $this->container->charset ? 
            $this->container->charset : 'utf-8';

        $contentType = $this->container->contentType ? 
            $this->container->contentType : 'text/html';

        $this->container->response->create(
            $this->container->content,
            $statusCode,
            $charset,
            $defaultHeaders
        );

        $srvProtocol = $this->container->request->server('SERVER_PROTOCOL');
        if ($srvProtocol == 'HTTP/1.0') {
            $this->container->response->setProtocolVersion('1.0');
        }
        
        $this->container->response->setContentType($contentType);
    }
}
