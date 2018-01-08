<?php

namespace Dewep\Providers;

use Dewep\Interfaces\ProviderInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Dewep\Config;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class LoggerProvider implements ProviderInterface
{

    public static function handle()
    {
        $debug = Config::get('debug', false);
        $logfile = Config::dirTemp() . '/app.log';

        $logger = new Logger('app');
        $logger->pushHandler(new StreamHandler($logfile,
                $debug ? Logger::DEBUG : Logger::INFO));

        return $logger;
    }

}
