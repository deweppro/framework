<?php declare(strict_types=1);

namespace Dewep\Interfaces;

use Dewep\Http\Request;
use Dewep\Http\Response;

/**
 * Interface MiddlewareInterface
 *
 * @package Dewep\Interfaces
 */
interface MiddlewareInterface
{

    /**
     * @param \Dewep\Http\Request  $request
     * @param \Dewep\Http\Response $response
     * @param array                $params
     *
     * @return mixed
     */
    public function before(Request $request, Response $response, array $params);

    /**
     * @param \Dewep\Http\Request  $request
     * @param \Dewep\Http\Response $response
     * @param array                $params
     *
     * @return mixed
     */
    public function after(Request $request, Response $response, array $params);

}
