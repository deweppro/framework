<?php

namespace Dewep;

use Dewep\Console\Input;
use Dewep\Console\Output;
use Dewep\Handlers\BlankApp;
use Dewep\Http\Request;
use Dewep\Http\Response;
use Dewep\Interfaces\ApplicationInterface;
use Dewep\Interfaces\ConsoleInterface;

/**
 * Class Console
 * @package Dewep
 */
class Console extends BlankApp implements ApplicationInterface
{
    /**
     * @var string|null
     */
    protected $command = null;

    /**
     * @var array
     */
    protected $commands = [];

    /**
     * @var Input
     */
    protected $input;

    /**
     * @var Output
     */
    protected $output;

    /**
     * Console constructor.
     */
    public function __construct()
    {
        $this->commands = Config::get('console', []);

        foreach ($_SERVER['argv'] as $id => $value) {
            if ($value === $_SERVER['SCRIPT_FILENAME']) {
                $this->command = $_SERVER['argv'][$id + 1] ?? null;

                break;
            }
        }

        $this->input = new Input();
        $this->output = new Output();
    }

    /**
     *
     */
    public function bootstrap()
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
        $this->input->build();
        $object->handler($this->input, $this->output);

        exit(0);
    }

    /**
     *
     */
    protected function commandsList()
    {
        $this->output->danger('Commands list:');
        foreach ($this->commands as $name => $handler) {
            $this->output->danger(
                sprintf(
                    "\t%s: %s".PHP_EOL,
                    $name,
                    $this->call([$handler, 'help'], [])
                )
            );
        }
    }

    /**
     * @return Request
     */
    public function request(): Request
    {

    }

    /**
     * @return Response
     */
    public function response(): Response
    {

    }
}
