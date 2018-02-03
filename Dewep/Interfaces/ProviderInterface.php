<?php

namespace Dewep\Interfaces;

use Dewep\Config;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
interface ProviderInterface
{
    /**
     * ProviderInterface constructor.
     * @param array $config
     */
    public function __construct(array $config);
}
