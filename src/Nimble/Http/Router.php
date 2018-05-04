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

use Nimble\Http\Exception\NotFoundException;

class Router  
{
    private $routerMap;

    private $routerCallback;

    public static function build(Application $app, $routerMap, callable $routerCallback)
    {
        $r = new Router($routerMap, $routerCallback, $app);
        return $r->parseWebRouter($app);
    }

    public function __construct($routerMap, $routerCallback)
    {
        $this->routerMap = $routerMap;
        $this->routerCallback = $routerCallback;
    }

    private function parseWebRouter(Application $app)
    {
        $rouerMap = $this->formatRouterMap();
        $requestPath = call_user_func_array($this->routerCallback, [$app]);
        foreach ($rouerMap as $url => $ctl) {
            if ($url === $requestPath) {
                return $ctl;
            }
        }
        throw new NotFoundException(sprintf("Request Path \"%s\" Not Found", $requestPath));
    }

    private function formatRouterMap()
    {
        $arrRoute2Ctl = [];
        foreach($this->routerMap as $uri => $map) {
            if (is_array($map)) {
                foreach($map as $suri => $smap) {
                    $urlPath = "{$uri}/{$suri}";
                    $arrRoute2Ctl[$urlPath] = $smap;
                }
            } else {
                $arrRoute2Ctl[$uri] = $map;
            }
        }
        $url2Map = [];
        foreach($arrRoute2Ctl as $uri => $map) {
            $urlPath = self::safeFilterUriPath($uri);
            $url2Map[$urlPath] = $map;
        }
        return $url2Map;
    }

    private static function safeFilterUriPath($uri)
    {
        return '/'. strtolower(trim($uri, " \t\n\r\0\x0B/"));
    }
}