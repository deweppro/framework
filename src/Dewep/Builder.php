<?php

declare(strict_types=1);

namespace Dewep;

final class Builder
{
    public const DEFAULT_METHOD = 'handler';

    public static function makes(array $class, ?string $handler = null): void
    {
        if (!empty($class)) {
            foreach ($class as $call => $params) {
                $params = is_array($params) ? $params : [$params];
                self::make((string)$call, $handler, $params);
            }
        }
    }

    /**
     * @return mixed
     */
    public static function make(string $class, ?string $handler = null, array $params = [])
    {
        [$call, $method] = explode('::', $class, 2);

        if (!empty($handler) && is_string($handler)) {
            $method = $handler;
        } elseif (empty($method) || 'class' === $method) {
            $method = self::DEFAULT_METHOD;
        }

        unset($params['_']);

        return self::call(new $call(), $method, $params);
    }

    /**
     * @return mixed
     */
    public static function call(
        object $object,
        string $method,
        array $arguments = []
    ) {
        return call_user_func_array([$object, $method], $arguments);
    }
}
