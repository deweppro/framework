<?php

namespace Dewep\Middleware;

use Psr\Http\Message\ResponseInterface as Res;
use Psr\Http\Message\ServerRequestInterface as Req;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Builder
{

    public static function makes(array $middlewares, Req $request, Res $response)
    {
        if (!empty($middlewares)) {
            foreach ($middlewares as $middleware) {
                call_user_func_array([$middleware, 'handle'],
                    [&$request, &$response]);
            }
        }
    }

    public static function make(string $middleware, Req $request, Res $response)
    {
        return call_user_func_array($middleware, [&$request, &$response]);
    }

}
