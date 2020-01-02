<?php

declare(strict_types=1);

namespace Dewep\Interfaces;

use Dewep\Console\Input;
use Dewep\Console\Output;

interface ConsoleInterface
{
    public function setup(Input $input): void;

    public function help(): string;

    public function handler(Input $input, Output $output): void;
}
