<?php

declare(strict_types=1);

namespace Dewep\Middleware;

use Dewep\Interfaces\MiddlewareInterface;

abstract class BaseClass implements MiddlewareInterface
{
    /** @var array */
    protected $params = [];

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public function getParam(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }
}
