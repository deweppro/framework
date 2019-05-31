<?php declare(strict_types=1);

namespace Dewep\Providers;

use Dewep\Config;
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
     * @param array $config
     *
     * @return mixed|\Monolog\Logger
     * @throws \Exception
     */
    public function handler(array $config)
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
