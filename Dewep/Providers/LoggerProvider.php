<?php

namespace Dewep\Providers;

use Dewep\Config;
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
     */
    public function __construct(Config $config)
    {
        $debug   = $config::get('debug', false);
        $logfile = $config::dirTemp().'/app.log';

        $logger = new Logger('app');
        $logger->pushHandler(
            new StreamHandler(
                $logfile,
                $debug ? Logger::DEBUG : Logger::INFO
            )
        );

        return $logger;
    }

}
