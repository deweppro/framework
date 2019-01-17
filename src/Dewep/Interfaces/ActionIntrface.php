<?php

namespace Dewep\Interfaces;

/**
 * Interface ActionIntrface
 *
 * @package Dewep\Interfaces
 */
interface ActionIntrface
{
    /**
     * @return mixed
     */
    public function all();

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function has(string $key);

    /**
     * @param string $key
     * @param        $default
     *
     * @return mixed
     */
    public function get(string $key, $default);

    /**
     * @param string $key
     * @param        $value
     *
     * @return mixed
     */
    public function set(string $key, $value);

    /**
     * @param string $key
     * @param        $value
     *
     * @return mixed
     */
    public function add(string $key, $value);

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function remove(string $key);
}
