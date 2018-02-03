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
     * @param Config $config
     */
    public function __construct(Config $config);
}
