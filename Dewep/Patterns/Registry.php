<?php

/*
 * The MIT License
 *
 * Copyright 2017 Mikhail Knyazhev <markus621@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Dewep\Patterns;

/**
 * Description of Registry
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
abstract class Registry
{

    protected static $__registry = [];

    /**
     *
     * @param string $key
     * @param type $value
     * @throws RuntimeException
     */
    public static function set(string $key, $value)
    {
        self::$__registry[self::__class()][$key] = static::value($value);
    }

    /**
     *
     * @param string $key
     * @param type $value
     */
    public static function exist(string $key, $value)
    {
        if (!self::has($key)) {
            self::set($key, $value);
        }
    }

    /**
     *
     * @param string $key
     * @param type $default
     * @return type
     */
    public static function get(string $key, $default = null)
    {
        return self::$__registry[self::__class()][$key] ?? $default;
    }

    /**
     *
     * @param string $key
     * @return type
     */
    public static function has(string $key)
    {
        return isset(self::$__registry[self::__class()][$key]);
    }

    /**
     *
     * @param string $key
     */
    public static function remove(string $key)
    {
        unset(self::$__registry[self::__class()][$key]);
    }

    /**
     *
     */
    public static function reset()
    {
        unset(self::$__registry[self::__class()]);
    }

    /**
     *
     * @return array
     */
    public static function all(): array
    {
        if (!isset(self::$__registry[self::__class()])) {
            return [];
        }
        return array_keys(self::$__registry[self::__class()]);
    }

    /**
     *
     * @return string
     */
    final protected static function __class(): string
    {
        return get_called_class();
    }

    /**
     *
     * @param type $value
     * @return type
     */
    protected static function value($value)
    {
        return $value;
    }

    final private function __construct()
    {

    }

    final private function __wakeup()
    {

    }

    final private function __clone()
    {

    }

}
