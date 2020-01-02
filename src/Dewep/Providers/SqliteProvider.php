<?php

declare(strict_types=1);

namespace Dewep\Providers;

use Dewep\Config;
use Dewep\Interfaces\ProviderInterface;
use Dewep\Sqlite;

final class SqliteProvider implements ProviderInterface
{
    public function handler(array $config): Sqlite
    {
        $filename = sprintf(
            '%s/%s',
            Config::storagePath(),
            $config['filename'] ?? 'sqlite.db'
        );

        return new Sqlite($filename);
    }
}
