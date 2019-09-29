<?php declare(strict_types=1);

namespace Dewep\Providers\Predis;

/**
 * Class GetJsonCommand
 *
 * @example getjson(key)
 *
 * @package Providers
 */
class GetJsonCommand extends \Predis\Command\Command
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'GET';
    }

    /**
     * @param string $data
     *
     * @return mixed|string
     */
    public function parseResponse($data)
    {
        return json_decode($data, true) ?? $data;
    }
}
