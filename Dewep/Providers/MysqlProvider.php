<?php

namespace Dewep\Providers;

use Dewep\Config;
use Dewep\Interfaces\ProviderInterface;
use Dewep\Mysql;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class MysqlProvider implements ProviderInterface
{
    /**
     * MysqlProvider constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $cfg = $config::get('mysql', []);

        return new Mysql(
            $cfg['host'] ?? 'localhost',
            $cfg['port'] ?? 3306,
            $cfg['dbname'] ?? 'default',
            $cfg['login'] ?? '',
            $cfg['password'] ?? ''
        );
    }

}
