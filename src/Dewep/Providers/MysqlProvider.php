<?php

declare(strict_types=1);

namespace Dewep\Providers;

use Dewep\Interfaces\ProviderInterface;
use Dewep\Mysql;

final class MysqlProvider implements ProviderInterface
{
    public function handler(array $config): Mysql
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
