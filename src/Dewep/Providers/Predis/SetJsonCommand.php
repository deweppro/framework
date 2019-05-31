<?php declare(strict_types=1);

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
    /**
     * @return string
     */
    public function getId()
    {
        return 'SETEX';
    }

    /**
     * @param array $arguments
     *
     * @return array
     */
    protected function filterArguments(array $arguments)
    {
        $arguments[2] = json_encode($arguments[2]);

        return $arguments;
    }
}
