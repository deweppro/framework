<?php

namespace Dewep\Providers;

use Dewep\Config;
use Dewep\Interfaces\ApplicationInterface;
use Dewep\Interfaces\ProviderInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class LoggerProvider
 *
 * @package Dewep\Providers
 */
class LoggerProvider implements ProviderInterface
{
    /**
     * @return Logger
     * @throws \Exception
     */
    public function handler(ApplicationInterface $app, array $config)
    {
        $debug = !empty($config['debug'] ?? false) ? Logger::DEBUG : Logger::INFO;
        $logfile = sprintf(
            '%s/%s',
            Config::tempPath(),
            $config['filename'] ?? 'app.log'
        );

        $appname = (string)($config['name'] ?? 'app');
        $logger = new Logger($appname);
        $logger->pushHandler(new StreamHandler($logfile, $debug));

        return $logger;
    }
}
