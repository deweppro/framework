<?php

declare(strict_types=1);

namespace Dewep\Interfaces;

use Dewep\Http\Request;
use Dewep\Http\Response;

interface MiddlewareInterface
{
    public function before(
        Request $request,
        Response $response,
        array $params
    ): void;

    public function after(
        Request $request,
        Response $response,
        array $params
    ): void;
}
