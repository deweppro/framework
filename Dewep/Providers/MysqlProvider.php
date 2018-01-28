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
     * @return Mysql
     */
    public function handle()
    {
        $config = Config::get('mysql', []);

        return new Mysql(
            $config['host'] ?? 'localhost',
            $config['port'] ?? 3306,
            $config['dbname'] ?? 'default',
            $config['login'] ?? '',
            $config['password'] ?? ''
        );
    }

}
