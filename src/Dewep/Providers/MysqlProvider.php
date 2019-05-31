<?php declare(strict_types=1);

namespace Dewep\Providers;

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
     * @param array $config
     *
     * @return \Dewep\Mysql|mixed
     */
    public function handler(array $config)
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
