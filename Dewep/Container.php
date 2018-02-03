<?php

namespace Dewep;

use Dewep\Patterns\Registry;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Container extends Registry
{

    /**
     * @param string $key
     * @param $value
     */
    public static function exist(string $key, $value)
    {
        if (!isset(self::$__registry[self::__class()][$key])) {
            self::set($key, $value);
        }
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
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
     * @param string $key
     * @return mixed|null
     */
    protected static function autoload(string $key)
    {
        $providers = Config::get('providers', []);
        if (isset($providers[$key])) {
            $class = $providers[$key];

            return new $class(Config::class);
        }

        return null;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function has(string $key)
    {
        if (isset(self::$__registry[self::__class()][$key])) {
            return true;
        }

        $providers = Config::get('providers', []);

        return isset($providers[$key]);
    }

    /**
     * @return array
     */
    public static function all(): array
    {
        $exist = self::$__registry[self::__class()] ?? [];
        $can   = Config::get('providers', []);

        $result = array_replace($can, $exist);

        return array_keys($result);
    }

    /**
     * @param $value
     * @return mixed
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

}
