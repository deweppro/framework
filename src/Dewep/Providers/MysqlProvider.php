<?php

namespace Dewep\Providers;

use Dewep\Interfaces\ProviderInterface;
use Dewep\Mysql;

/**
 * Class MysqlProvider
 *
 * @package Dewep\Providers
 */
class MysqlProvider implements ProviderInterface
{
    /** @var string */
    private $host;
    /** @var int */
    private $port;
    /** @var string */
    private $dbname;
    /** @var string */
    private $login;
    /** @var string */
    private $password;

    /**
     * MysqlProvider constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->host = (string)($config['host'] ?? 'localhost');
        $this->port = (int)($config['port'] ?? 3306);
        $this->dbname = (string)($config['dbname'] ?? 'default');
        $this->login = (string)($config['login'] ?? '');
        $this->password = (string)($config['password'] ?? '');
    }

    /**
     * @return Mysql|mixed
     */
    public function handler()
    {
        return new Mysql($this->host, $this->port, $this->dbname, $this->login, $this->password);
    }
}
