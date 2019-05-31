<?php declare(strict_types=1);

namespace Dewep\Middleware;

use Dewep\Interfaces\MiddlewareInterface;

abstract class BaseClass implements MiddlewareInterface
{
    /** @var array */
    protected $params = [];

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getParam(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }
}
