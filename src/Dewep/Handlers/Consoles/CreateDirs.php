<?php

declare(strict_types=1);

namespace Dewep\Handlers\Consoles;

use Dewep\Config;
use Dewep\Console\Input;
use Dewep\Console\Output;
use Dewep\Interfaces\ConsoleInterface;

final class CreateDirs implements ConsoleInterface
{
    public function setup(Input $input): void
    {
    }

    public function help(): string
    {
        return 'Restore the system directory structure.';
    }

    public function handler(Input $input, Output $output): void
    {
        Config::restoreFolderStructure();
    }
}
