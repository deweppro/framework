<?php

namespace Dewep\Interfaces;

/**
 * Interface MiddlewareInterface
 *
 * @package Dewep\Interfaces
 */
interface MiddlewareInterface
{

    /**
     * @param ApplicationInterface $app
     * @param array                $params
     *
     * @return mixed
     */
    public function before(ApplicationInterface $app, array $params);

    /**
     * @param ApplicationInterface $app
     * @param array                $params
     *
     * @return mixed
     */
    public function after(ApplicationInterface $app, array $params);

}
