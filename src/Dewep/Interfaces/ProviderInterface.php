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
     * @param ApplicationInterface $app
     * @param array                $config
     *
     * @return mixed
     */
    public function handler(ApplicationInterface $app, array $config);
}
