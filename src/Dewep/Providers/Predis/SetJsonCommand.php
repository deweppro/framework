<?php

declare(strict_types=1);

namespace Dewep\Providers\Predis;

/**
 * @example setjson(key, ttl, array-value)
 */
final class SetJsonCommand extends \Predis\Command\Command
{
    public function getId(): string
    {
        return 'SETEX';
    }

    protected function filterArguments(array $arguments): array
    {
        $arguments[2] = json_encode($arguments[2]);

        return $arguments;
    }
}
