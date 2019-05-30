<?php

namespace Dewep\Handlers;

use Dewep\Interfaces\ApplicationInterface;

abstract class BlankApp implements ApplicationInterface
{

    /**
     * @param mixed $function
     * @param array $arguments
     *
     * @return mixed
     */
    public function call($function, array $arguments)
    {
        array_unshift($arguments, $this);

        return call_user_func_array($function, $arguments);
    }
}
