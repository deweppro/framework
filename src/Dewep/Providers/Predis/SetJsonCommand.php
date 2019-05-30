<?php

namespace Dewep\Providers\Predis;

/**
 * Class SetJsonCommand
 *
 * @example setjson(key, ttl, array-value)
 *
 * @package Providers
 */
class SetJsonCommand extends \Predis\Command\Command
{
    public function getId()
    {
        return 'SETEX';
    }

    protected function filterArguments(array $arguments)
    {
        $arguments[2] = json_encode($arguments[2]);

        return $arguments;
    }
}
