<?php

namespace Dewep\Patterns;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
abstract class SingletonRegistry
{
    /**
     * @var null|SingletonRegistry
     */
    static private $_instance = null;
    /**
     * @var array
     */
    private $_registry = array();

    /**
     * SingletonRegistry constructor.
     */
    final private function __construct()
    {

    }

    /**
     * @param $key
     * @param $object
     */
    final public static function set($key, $object)
    {
        self::getInstance()->_registry[$key] = $object;
    }

    /**
     * @return SingletonRegistry|null
     */
    final private static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    final public static function get($key)
    {
        return self::getInstance()->_registry[$key] ?? null;
    }

    /*
     *
     */

    /**
     * @param $key
     */
    final public static function remove($key)
    {
        unset(self::getInstance()->_registry[$key]);
    }

    final private function __wakeup()
    {

    }

    final private function __clone()
    {

    }

}
