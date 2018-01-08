<?php

namespace Dewep;

use Dewep\Patterns\Registry;
use Dewep\Config;

/**
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
