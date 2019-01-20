<?php

namespace Dewep\Providers;

use Dewep\Interfaces\ApplicationInterface;
use Dewep\Interfaces\ProviderInterface;
use Dewep\Mysql;

/**
 * Class MysqlProvider
 *
 * @package Dewep\Providers
 */
class MysqlProvider implements ProviderInterface
{
    /**
     * @return Mysql|mixed
     */
    public function handler(ApplicationInterface $app, array $config)
    {
        return new Mysql(
            (string)($config['host'] ?? 'localhost'),
            (int)($config['port'] ?? 3306),
            (string)($config['dbname'] ?? 'default'),
            (string)($config['login'] ?? ''),
            (string)($config['password'] ?? '')
        );
    }
}
