<?php

namespace Dewep\Providers;

use Dewep\Config;
use Dewep\Interfaces\ApplicationInterface;
use Dewep\Interfaces\ProviderInterface;
use Dewep\Sqlite;

/**
 * Class SqliteProvider
 *
 * @package Dewep\Providers
 */
class SqliteProvider implements ProviderInterface
{
    /**
     * @return Sqlite
     */
    public function handler(ApplicationInterface $app, array $config)
    {
        $filename = sprintf(
            '%s/%s',
            Config::storagePath(),
            $config['filename'] ?? 'sqlite.db'
        );

        return new Sqlite($filename);
    }
}
