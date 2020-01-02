<?php

declare(strict_types=1);

namespace Dewep;

use Dewep\Patterns\Registry;

final class Container extends Registry
{
    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        if (isset(self::$__registry[self::__class()][$key])) {
            return self::$__registry[self::__class()][$key];
        }

        $load = self::autoload($key);
        if (null !== $load) {
            return self::$__registry[self::__class()][$key] = $load;
        }

        return $default;
    }

    public static function has(string $key): bool
    {
        if (isset(self::$__registry[self::__class()][$key])) {
            return true;
        }

        $providers = Config::get('providers', []);

        return isset($providers[$key]);
    }

    public static function all(): array
    {
        $exist = self::$__registry[self::__class()] ?? [];
        $can   = Config::get('providers', []);

        $result = array_replace($can, $exist);

        return array_keys($result);
    }

    /**
     * @return mixed|null
     */
    protected static function autoload(string $key)
    {
        $providers = Config::get('providers', []);

        if (!empty($providers[$key]['_'])) {
            $obj = Builder::make(
                $providers[$key]['_'],
                null,
                [$providers[$key]]
            );
            if (false !== $obj) {
                return $obj;
            }
        }

        return null;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected static function value($value)
    {
        if ($value instanceof \Closure) {
            return $value();
        } elseif (is_string($value)) {
            $obj = Builder::make($value);
            if (false !== $obj) {
                return $obj;
            }
        }

        return $value;
    }
}
