<?php

declare(strict_types=1);

namespace Dewep\Providers;

use Dewep\Config;
use Dewep\Interfaces\ProviderInterface;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

final class LoggerProvider implements ProviderInterface
{
    public function handler(array $config): Logger
    {
        $debug   = !empty($config['debug'] ?? false) ? Logger::DEBUG : Logger::INFO;
        $logfile = sprintf(
            '%s/%s',
            Config::tempPath(),
            $config['filename'] ?? 'app.log'
        );

        $appname = (string) ($config['name'] ?? 'app');
        $logger  = new Logger($appname);

        try {
            $logger->pushHandler(new StreamHandler($logfile, $debug));
        } catch (\Exception $e) {
            $logger->pushHandler(
                new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, $debug)
            );
        }

        return $logger;
    }
}
