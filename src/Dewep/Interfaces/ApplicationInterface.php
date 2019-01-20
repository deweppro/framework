<?php

namespace Dewep\Interfaces;

use Dewep\Http\Request;
use Dewep\Http\Response;

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
     * @return Request
     */
    public function request(): Request;

    /**
     * @return Response
     */
    public function response(): Response;

    /**
     * @param mixed $function
     * @param array $arguments
     *
     * @return mixed
     */
    public function call($function, array $arguments);


}
