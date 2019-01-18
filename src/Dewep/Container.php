<?php

namespace Dewep;

use Dewep\Handlers\BlankApp;
use Dewep\Interfaces\ProviderInterface;
use Dewep\Patterns\Registry;

class Container extends Registry
{
    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
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
     *
     * @return null|ProviderInterface
     */
    protected static function autoload(string $key)
    {
        $providers = Config::get('providers', []);

        if (!empty($providers[$key]['_'])) {
            $obj = Builder::make(
                new BlankApp(),
                $providers[$key]['_'],
                null,
                $providers[$key]
            );
            if ($obj !== false) {
                return $obj;
            }
        }

        return null;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public static function has(string $key): bool
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
        $can = Config::get('providers', []);

        $result = array_replace($can, $exist);

        return array_keys($result);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    protected static function value($value)
    {
        if ($value instanceof \Closure) {
            return $value();
        } elseif (is_string($value)) {
            $obj = Builder::make(new BlankApp(), $value, null, []);
            if ($obj !== false) {
                return $obj;
            }
        }

        return $value;
    }

}
