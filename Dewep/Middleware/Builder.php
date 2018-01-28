<?php

namespace Dewep\Middleware;

use Dewep\Http\Request;
use Dewep\Http\Response;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Builder
{

    /**
     * @param array $middlewares
     * @param Request $request
     * @param Response $response
     * @param string|null $default
     */
    public static function makes(
        array $middlewares,
        Request $request,
        Response $response,
        string $default = null
    ) {
        if (!empty($middlewares)) {
            foreach ($middlewares as $middleware) {
                self::make($middleware, $request, $response, $default);
            }
        }
    }

    /**
     * @param string $middleware
     * @param Request $request
     * @param Response $response
     * @param string|null $default
     * @return mixed
     */
    public static function make(
        string $middleware,
        Request $request,
        Response $response,
        string $default = null
    ) {
        @list($class, $method) = explode('::', $middleware);

        $obj = new $class($request, $response);

        $method = $method ?? $default ?? 'handle';

        if (method_exists($obj, $method)) {
            $obj->$method();
        }

        return $obj;
    }

}
