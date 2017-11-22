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

namespace Dewep;

use Dewep\Patterns\Registry;
use Dewep\Config;

/**
 * Description of Container
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Container extends Registry
{

    /**
     * Можно передать:
     * - замыкание
     * - объект
     * - строку для вызыва функции
     * @param string $key
     * @param type $value
     */
    protected static function value($value)
    {
        if ($value instanceof \Closure) {
            return $value();
        } elseif (is_string($value)) {
            $obj = call_user_func($value);
            if ($obj !== false) {
                return $obj;
            }
        }
        return $value;
    }

    /**
     *
     * @param string $key
     * @return type
     */
    protected static function autoload(string $key)
    {
        $providers = Config::get('providers', []);
        if (isset($providers[$key])) {
            return call_user_func([$providers[$key], 'handle']);
        }
        return null;
    }

    /**
     *
     * @param string $key
     * @param type $value
     */
    public static function exist(string $key, $value)
    {
        if (!isset(self::$__registry[self::__class()][$key])) {
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
        if (isset(self::$__registry[self::__class()][$key])) {
            return self::$__registry[self::__class()][$key];
        }

        $load = self::autoload($key);

        return $load ?? $default;
    }

    /**
     *
     * @param string $key
     * @return type
     */
    public static function has(string $key)
    {
        if (isset(self::$__registry[self::__class()][$key])) {
            return true;
        }

        $providers = Config::get('providers', []);
        if (isset($providers[$key])) {
            return true;
        }

        return false;
    }

    /**
     *
     * @return array
     */
    public static function all(): array
    {
        $exist = self::$__registry[self::__class()] ?? [];
        $can = Config::get('providers', []);

        $result = array_replace($can, $exist);

        return array_keys($result);
    }

}
