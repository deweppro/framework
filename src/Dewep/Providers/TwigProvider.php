<?php declare(strict_types=1);

namespace Dewep\Providers;

use Dewep\Config;
use Dewep\Interfaces\ProviderInterface;

/**
 * Class TwigProvider
 *
 * @package Providers
 */
class TwigProvider implements ProviderInterface
{
    /**
     * @param array $config
     *
     * @return mixed|\Twig\Environment
     */
    public function handler(array $config)
    {
        $loader = new \Twig\Loader\FilesystemLoader(Config::resourcesPath());
        $twig = new \Twig\Environment(
            $loader, [
                'cache'         => Config::tempPath(),
                'auto_reload'   => true,
                'optimizations' => -1,
            ]
        );

        return $twig;
    }
}
