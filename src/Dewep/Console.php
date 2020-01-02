<?php

declare(strict_types=1);

namespace Dewep;

use Dewep\Console\Input;
use Dewep\Console\Output;
use Dewep\Interfaces\ApplicationInterface;
use Dewep\Interfaces\ConsoleInterface;

final class Console implements ApplicationInterface
{
    /** @var string|null */
    private $command;

    /** @var array */
    private $commands = [];

    /** @var Input */
    private $input;

    /** @var Output */
    private $output;

    public function __construct()
    {
        $this->commands = Config::get('console', []);

        foreach ($_SERVER['argv'] as $id => $value) {
            if ($value === $_SERVER['SCRIPT_FILENAME']) {
                $this->command = $_SERVER['argv'][$id + 1] ?? null;

                break;
            }
        }

        $this->input  = new Input();
        $this->output = new Output();
    }

    public function bootstrap(): void
    {
        if (!isset($this->commands[$this->command])) {
            $this->commandsList();
            exit(1);
        }

        $handler = $this->commands[$this->command];

        $object = new $handler();

        if (!$object instanceof ConsoleInterface) {
            $this->output->danger(
                sprintf(
                    '%s is not inherited from ConsoleInterface.',
                    $handler
                )
            );
            exit(2);
        }

        $object->setup($this->input);
        $this->input->initialize();
        $object->handler($this->input, $this->output);

        exit(0);
    }

    private function commandsList(): void
    {
        $this->output->danger('Commands list:');
        foreach ($this->commands as $name => $handler) {
            $this->output->danger(
                sprintf(
                    "\t%s: %s",
                    $name,
                    Builder::call(new $handler(), 'help', [])
                )
            );
        }
    }
}
