<?php declare(strict_types=1);

namespace Dewep\Providers;

use Dewep\Config;
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
     * @param array $config
     *
     * @return \Dewep\Sqlite|mixed
     */
    public function handler(array $config)
    {
        $filename = sprintf(
            '%s/%s',
            Config::storagePath(),
            $config['filename'] ?? 'sqlite.db'
        );

        return new Sqlite($filename);
    }
}
