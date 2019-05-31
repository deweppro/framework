<?php declare(strict_types=1);

namespace Dewep;

/**
 * Class Builder
 *
 * @package Dewep
 */
class Builder
{
    const DEFAULT_METHOD = 'handler';

    /**
     * @param array       $class
     * @param string|null $handler
     */
    public static function makes(array $class, ?string $handler = null)
    {
        if (!empty($class)) {
            foreach ($class as $call => $params) {
                $params = is_array($params) ? $params : [$params];
                self::make($call, $handler, $params);
            }
        }
    }

    /**
     * @param string      $class
     * @param string|null $handler
     * @param array       $params
     *
     * @return mixed
     */
    public static function make(string $class, ?string $handler = null, array $params = [])
    {
        @list($call, $method) = explode('::', $class, 2);

        if (!empty($handler) && is_string($handler)) {
            $method = $handler;
        } elseif (empty($method) || $method === 'class') {
            $method = self::DEFAULT_METHOD;
        }

        unset($params['_']);

        return self::call([$call, $method], $params);
    }

    /**
     * @param mixed $function
     * @param array $arguments
     *
     * @return mixed
     */
    public static function call($function, array $arguments = [])
    {
        return call_user_func_array($function, $arguments);
    }

}
