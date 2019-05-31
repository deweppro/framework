<?php declare(strict_types=1);

namespace Dewep\Interfaces;

use Dewep\Console\Input;
use Dewep\Console\Output;

/**
 * Interface ConsoleInterface
 *
 * @package Dewep\Interfaces
 */
interface ConsoleInterface
{
    /**
     * @return string
     */
    public function help(): string;

    /**
     * @param Input $input
     *
     * @return mixed
     */
    public function setup(Input $input);

    /**
     * @param Input  $input
     * @param Output $output
     *
     * @return mixed
     */
    public function handler(Input $input, Output $output);
}
