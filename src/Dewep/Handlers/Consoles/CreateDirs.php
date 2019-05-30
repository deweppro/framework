<?php


namespace Dewep\Handlers\Consoles;


use Dewep\Config;
use Dewep\Console\Input;
use Dewep\Console\Output;
use Dewep\Interfaces\ConsoleInterface;

class CreateDirs implements ConsoleInterface
{
    public function help(): string
    {
        return 'Restore the system directory structure.';
    }

    public function setup(Input $input)
    {
        // TODO: Implement setup() method.
    }

    public function handler(Input $input, Output $output)
    {
        Config::restoreFolderStructure();
    }
}
