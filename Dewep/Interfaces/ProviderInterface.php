<?php

namespace Dewep\Interfaces;

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

    /**
     * @return mixed
     */
    public function handler();
}
