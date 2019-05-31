<?php declare(strict_types=1);

namespace Dewep\Providers;

use Dewep\Interfaces\ProviderInterface;
use Dewep\Providers\Predis\GetJsonCommand;
use Dewep\Providers\Predis\RedisProfileInterface;
use Dewep\Providers\Predis\SetJsonCommand;

/**
 * Class RedisProvider
 *
 * @package Providers
 */
class RedisProvider implements ProviderInterface
{
    /**
     * @param array $config
     *
     * @return mixed|\Predis\Client
     */
    public function handler(array $config)
    {
        $client = new \Predis\Client(
            [
                'scheme' => $config['host'] ?? '127.0.0.1',
                'host'   => (int)($config['port'] ?? 6379),
                'port'   => $config['scheme'] ?? 'tcp',
            ]
        );

        /** @var RedisProfileInterface $profile */
        $profile = $client->getProfile();

        $profile->defineCommand('setjson', SetJsonCommand::class);
        $profile->defineCommand('getjson', GetJsonCommand::class);

        return $client;
    }

}
