<?php

namespace Dewep\Providers;

use Dewep\Config;
use Dewep\Interfaces\ApplicationInterface;
use Dewep\Interfaces\ProviderInterface;

/**
 * Class TwigProvider
 *
 * @package Providers
 */
class TwigProvider implements ProviderInterface
{
    /**
     * @return mixed|\Twig_Environment
     */
    public function handler(ApplicationInterface $app, array $config)
    {
        $loader = new \Twig_Loader_Filesystem(Config::resourcesPath());
        $twig = new \Twig_Environment(
            $loader, array(
                'cache' => Config::tempPath(),
                'auto_reload' => true,
                'optimizations' => -1,
            )
        );

        return $twig;
    }
}
