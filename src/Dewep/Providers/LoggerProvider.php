<?php

namespace Dewep\Providers;

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
    /** @var bool */
    private $debug;
    /** @var string */
    private $logfile;
    /** @var string */
    private $appname;

    /**
     * LoggerProvider constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->debug = !empty($config['debug'] ?? false);
        $this->logfile = sprintf(
            '%s/%s',
            $config['_']['temp'] ?? sys_get_temp_dir(),
            $config['filename'] ?? 'app.log'
        );

        $this->appname = (string)($config['name'] ?? 'app');
    }

    /**
     * @return Logger
     * @throws \Exception
     */
    public function handler(): Logger
    {
        $logger = new Logger($this->appname);
        $logger->pushHandler(
            new StreamHandler(
                $this->logfile,
                $this->debug ? Logger::DEBUG : Logger::INFO
            )
        );

        return $logger;
    }
}
