<?php

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
    public function getId()
    {
        return 'GET';
    }

    public function parseResponse($data)
    {
        return json_decode($data, true) ?? $data;
    }
}
