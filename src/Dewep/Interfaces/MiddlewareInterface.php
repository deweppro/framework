<?php declare(strict_types=1);

namespace Dewep\Interfaces;

/**
 * Interface MiddlewareInterface
 *
 * @package Dewep\Interfaces
 */
interface MiddlewareInterface
{

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function before(array $params);

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function after(array $params);

}
