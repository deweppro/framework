<?php

namespace Dewep\Interfaces;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
interface ActionIntrface
{

    public function all();

    public function has(string $key);

    public function get(string $key, $default);

    public function set(string $key, $value);

    public function add(string $key, $value);

    public function remove(string $key);
}
