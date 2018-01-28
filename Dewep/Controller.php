<?php

namespace Dewep;

use Dewep\Http\Request;
use Dewep\Http\Response;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
abstract class Controller
{
    /** @var Request */
    protected $request;
    /** @var Response */
    protected $response;
    /** @var \Monolog\Logger */
    protected $logger;

    /**
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request  = $request;
        $this->response = $response;

        $this->logger = Container::get('logger');
    }

}
