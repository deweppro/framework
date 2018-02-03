<?php

namespace Dewep\Providers;

use Dewep\Interfaces\ProviderInterface;
use Dewep\Mysql;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class MysqlProvider implements ProviderInterface
{
    /**
     * MysqlProvider constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        return new Mysql(
            $config['host'] ?? 'localhost',
            $config['port'] ?? 3306,
            $config['dbname'] ?? 'default',
            $config['login'] ?? '',
            $config['password'] ?? ''
        );
    }

}
