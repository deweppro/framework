<?php

namespace Dewep\Middleware;

use Dewep\Config;
use Dewep\Http\Request;
use Dewep\Http\Response;

/**
 * Class Builder
 *
 * @package Dewep\Middleware
 */
class Builder
{

    /**
     * @param array    $middlewares
     * @param Request  $request
     * @param Response $response
     * @param string   $default
     */
    public static function makes(array $middlewares, Request $request, Response $response, string $default = 'handle')
    {
        if (!empty($middlewares)) {
            foreach ($middlewares as $middleware => $params) {

                self::make($middleware, $params ?? [], $request, $response, $default);
            }
        }
    }

    /**
     * @param string   $middleware
     * @param array    $params
     * @param Request  $request
     * @param Response $response
     * @param string   $default
     *
     * @return mixed
     */
    public static function make(
        string $middleware,
        array $params,
        Request $request,
        Response $response,
        string $default = 'handle'
    ) {
        @list($class, $method) = explode('::', $middleware);

        $params['_'] = Config::getPaths();

        $obj = new $class($request, $response, $params);

        $method = $method ?? $default;

        if (method_exists($obj, $method)) {
            $obj->$method();
        }

        return $obj;
    }

}
