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

use Dewep\Exception\RuntimeException;

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
    final public static function set(string $key, $value)
    {
        self::$__registry[$key] = $value;
    }

    /**
     *
     * @param string $key
     * @param type $default
     * @return type
     */
    final public static function get(string $key, $default = null)
    {
        return self::$__registry[$key] ?? $default;
    }

    /**
     *
     * @param string $key
     */
    final public static function remove(string $key)
    {
        unset(self::$__registry[$key]);
    }

    /**
     *
     */
    final public static function reset()
    {
        unset(self::$__registry[$key]);
    }

    /*
     *
     */

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
