<?php

namespace Dewep\Patterns;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
abstract class Singleton
{

    protected static $__instance;

    final private function __construct()
    {

    }

    /**
     * @return Singleton
     */
    final public static function getInstance()
    {
        if (is_null(self::$__instance)) {
            self::$__instance = new self;
        }

        return self::$__instance;
    }

    final private function __wakeup()
    {

    }

    final private function __clone()
    {

    }

}
