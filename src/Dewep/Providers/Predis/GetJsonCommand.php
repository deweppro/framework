<?php

declare(strict_types=1);

namespace Dewep\Providers\Predis;

/**
 * @example getjson(key)
 */
final class GetJsonCommand extends \Predis\Command\Command
{
    public function getId(): string
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
