<?php

declare(strict_types=1);

namespace Dewep\Interfaces;

interface ProviderInterface
{
    /**
     * @return mixed
     */
    public function handler(array $config);
}
