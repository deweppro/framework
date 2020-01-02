<?php

declare(strict_types=1);

namespace Dewep\Providers;

use Dewep\Config;
use Dewep\Interfaces\ProviderInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class TwigProvider implements ProviderInterface
{
    public function handler(array $config): Environment
    {
        $loader = new FilesystemLoader(Config::resourcesPath());

        return new Environment(
            $loader,
            [
                'cache'         => Config::tempPath(),
                'auto_reload'   => true,
                'optimizations' => -1,
            ]
        );
    }
}
