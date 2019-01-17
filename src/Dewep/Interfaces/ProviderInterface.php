<?php

namespace Dewep\Interfaces;

/**
 * Interface ProviderInterface
 *
 * @package Dewep\Interfaces
 */
interface ProviderInterface
{

    /**
     * ProviderInterface constructor.
     *
     * @param array $config
     */
    public function __construct(array $config);

    /**
     * @return mixed
     */
    public function handler();
}
