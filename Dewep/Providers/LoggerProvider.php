<?php

namespace Dewep\Providers;

use Dewep\Interfaces\ProviderInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class LoggerProvider implements ProviderInterface
{
    /**
     * LoggerProvider constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $debug   = $config['debug'] ?? false;
        $logfile = sprintf(
            '%s/%s',
            $config['_']['temp'] ?? sys_get_temp_dir(),
            $config['filename'] ?? 'app.log'
        );

        $logger = new Logger($config['name'] ?? 'app');
        $logger->pushHandler(
            new StreamHandler(
                $logfile,
                $debug ? Logger::DEBUG : Logger::INFO
            )
        );

        return $logger;
    }

}
