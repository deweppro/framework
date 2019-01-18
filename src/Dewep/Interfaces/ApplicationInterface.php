<?php

namespace Dewep\Interfaces;

interface ApplicationInterface
{
    /**
     * ApplicationInterface constructor.
     */
    public function __construct();

    /**
     * @throws \Exception
     */
    public function bootstrap();

    /**
     * @param mixed $function
     * @param array $arguments
     *
     * @return mixed
     */
    public function call($function, array $arguments);


}
