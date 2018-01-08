<?php

namespace Dewep\Interfaces;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
interface MiddlewareInterface
{

    public function handle(Request $request, Response $response);
}
