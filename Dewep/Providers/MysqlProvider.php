<?php

namespace Dewep\Providers;

use Dewep\Interfaces\ProviderInterface;
use Dewep\Config;
use Dewep\Mysql;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class MysqlProvider implements ProviderInterface
{

    public static function handle()
    {
        $config = Config::get('mysql', []);
        return new Mysql($config['host'] ?? 'localhost',
                $config['port'] ?? 3306, $config['dbname'] ?? 'default',
                $config['login'] ?? '', $config['password'] ?? '');
    }

}
