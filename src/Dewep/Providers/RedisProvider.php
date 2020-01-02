<?php

declare(strict_types=1);

namespace Dewep\Providers;

use Dewep\Interfaces\ProviderInterface;
use Dewep\Providers\Predis\GetJsonCommand;
use Dewep\Providers\Predis\RedisProfileInterface;
use Dewep\Providers\Predis\SetJsonCommand;
use Predis\Client;

final class RedisProvider implements ProviderInterface
{
    public function handler(array $config): Client
    {
        $client = new Client(
            [
                'scheme' => (string) ($config['scheme'] ?? 'tcp'),
                'host'   => (string) ($config['host'] ?? '127.0.0.1'),
                'port'   => (int) ($config['port'] ?? 6379),
            ]
        );

        /** @var RedisProfileInterface $profile */
        $profile = $client->getProfile();

        $profile->defineCommand('setjson', SetJsonCommand::class);
        $profile->defineCommand('getjson', GetJsonCommand::class);

        return $client;
    }
}
