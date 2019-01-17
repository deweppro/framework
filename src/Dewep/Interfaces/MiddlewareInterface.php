<?php

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
     * MiddlewareInterface constructor.
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $params
     */
    public function __construct(Request $request, Response $response, array $params);

    /**
     * @return mixed
     */
    public function before();

    /**
     * @return mixed
     */
    public function after();

    /**
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function getParams($key, $default = null);
}