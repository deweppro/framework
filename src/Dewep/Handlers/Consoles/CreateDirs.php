<?php declare(strict_types=1);

namespace Dewep\Handlers\Consoles;

use Dewep\Config;
use Dewep\Console\Input;
use Dewep\Console\Output;
use Dewep\Interfaces\ConsoleInterface;

/**
 * Class CreateDirs
 *
 * @package Dewep\Handlers\Consoles
 */
class CreateDirs implements ConsoleInterface
{
    /**
     * @return string
     */
    public function help(): string
    {
        return 'Restore the system directory structure.';
    }

    /**
     * @param \Dewep\Console\Input $input
     *
     * @return mixed|void
     */
    public function setup(Input $input)
    {
        // TODO: Implement setup() method.
    }

    /**
     * @param \Dewep\Console\Input  $input
     * @param \Dewep\Console\Output $output
     *
     * @return mixed|void
     */
    public function handler(Input $input, Output $output)
    {
        Config::restoreFolderStructure();
    }
}
