<?php

namespace Dewep;

use Dewep\Interfaces\ApplicationInterface;

/**
 * Class Builder
 *
 * @package Dewep
 */
class Builder
{
    const DEFAULT_METHOD = 'handle';

    /**
     * @param ApplicationInterface $app
     * @param array                $class
     * @param mixed                $handle
     */
    public static function makes(ApplicationInterface $app, array $class, $handle = null)
    {
        if (!empty($class)) {
            foreach ($class as $call => $params) {
                $params = is_array($params) ? $params : [$params];
                self::make($app, $call, $params, $handle);
            }
        }
    }

    /**
     * @param ApplicationInterface $app
     * @param string               $class
     * @param mixed                $handle
     * @param array                $params
     *
     * @return mixed
     */
    public static function make(ApplicationInterface $app, string $class, $handle, array $params)
    {
        @list($call, $method) = explode('::', $class, 2);

        if (!empty($handle) && is_string($handle)) {
            $method = $handle;
        } elseif (empty($method) || $method === 'class') {
            $method = self::DEFAULT_METHOD;
        }

        unset($params['_']);

        return $app->call([$call, $method], $params);
    }

}
