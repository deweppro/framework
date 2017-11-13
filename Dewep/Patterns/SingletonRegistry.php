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
 * Description of SingletonRegistry
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
abstract class SingletonRegistry
{

    static private $_instance = null;
    private $_registry = array();

    /**
     *
     * @return type
     */
    final private static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     *
     * @param type $key
     * @param type $object
     */
    final public static function set($key, $object)
    {
        self::getInstance()->_registry[$key] = $object;
    }

    /**
     *
     * @param type $key
     * @return type
     */
    final public static function get($key)
    {
        return self::getInstance()->_registry[$key] ?? null;
    }

    /**
     *
     * @param type $key
     */
    final public static function remove($key)
    {
        unset(self::getInstance()->_registry[$key]);
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
