<?php declare(strict_types=1);

namespace Dewep\Interfaces;

/**
 * Interface ProviderInterface
 *
 * @package Dewep\Interfaces
 */
interface ProviderInterface
{
    /**
     * @param array $config
     *
     * @return mixed
     */
    public function handler(array $config);
}
