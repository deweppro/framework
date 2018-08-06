<?php

namespace Dewep\Providers;

use Dewep\Interfaces\ProviderInterface;
use Dewep\Sqlite;

class SqliteProvider implements ProviderInterface
{
    /** @var string */
    private $filename;

    /**
     * SqliteProvider constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->filename = sprintf(
            '%s/%s',
            $config['_']['temp'] ?? sys_get_temp_dir(),
            $config['filename'] ?? 'sqlite.db'
        );
    }

    /**
     * @return Sqlite
     */
    public function handler()
    {
        return new Sqlite($this->filename);
    }
}
