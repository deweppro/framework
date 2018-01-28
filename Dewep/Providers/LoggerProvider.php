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
     * @return Logger
     */
    public function handle()
    {
        $debug   = Config::get('debug', false);
        $logfile = Config::dirTemp().'/app.log';

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
