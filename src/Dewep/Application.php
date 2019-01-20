<?php

namespace Dewep;

use Dewep\Handlers\BlankApp;
use Dewep\Handlers\Error;
use Dewep\Http\Request;
use Dewep\Http\Response;
use Dewep\Interfaces\ApplicationInterface;

/**
 * Class Application
 *
 * @package Dewep
 */
class Application extends BlankApp implements ApplicationInterface
{
    /** @var Request */
    protected $request;
    /** @var Response */
    protected $response;

    /** @var array */
    protected static $allowHeaders = [
        'Content-Type' => null,
    ];

    /**
     * Application constructor.
     */
    public function __construct()
    {
        parent::__construct();

        Error::bootstrap();
    }

    /**
     *
     */
    public static function cors()
    {
        $allowHeaders = Config::get('allowHeaders', []);

        $headers = [
            'Access-Control-Allow-Origin' => Config::get('domain', '*'),
            'Access-Control-Allow-Methods' => 'HEAD,OPTIONS,GET,POST,PUT,DELETE,TRACE',
            'Access-Control-Max-Age' => 0,
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Headers' => implode(
                ', ',
                array_keys(
                    array_replace(
                        array_flip($allowHeaders),
                        static::$allowHeaders
                    )
                )
            ),
            'Cache-Control' => 'no-cache',
            'Pragma' => 'no-cache',
        ];

        foreach ($headers as $key => $value) {
            header(sprintf('%s: %s', $key, $value), true);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit(0);
        }
    }

    /**
     * @return Request
     */
    public function request(): Request
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function response(): Response
    {
        return $this->response;
    }

    /**
     * @throws \Exception
     */
    public function bootstrap()
    {
        ob_start();

        /**
         * routes
         *
         * In a config it is possible to specify an array
         * of references and the controllers processing them.
         *
         * @example
         *         routes:
         *              /:
         *                  POST,GET: IndexController::index
         *
         * Or specify the controller that handles routing.
         *
         * @example
         *         routes: RoutingHelper::build
         *
         * @var array|string $routes
         */
        $routes = Config::get('routes', []);
        if (is_string($routes)) {
            $routes = Builder::make($this, $routes, null, []);
        }

        /**
         *  Build Request+Response
         */
        $this->request = Request::bootstrap($routes);
        $this->response = Response::bootstrap();

        /**
         * middleware
         */
        $middleware = Config::get('middleware', []);

        Builder::makes($this, $middleware['before'] ?? [], 'before');

        /**
         * call controllers
         */
        $content = $this->router();
        if ($content instanceof Response) {
            $response = $content;
        } else {
            $response = $this->response()
                ->setBody($content, Config::get('response'));
        }

        Builder::makes($this, $middleware['after'] ?? [], 'after');

        $err = ob_get_contents();
        ob_end_flush();

        /**
         * errors
         */
        if (!empty($err)) {
            Container::get('logger')->warning($err);
        }

        echo $response;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function router()
    {
        /** @var array $attributes */
        $attributes = $this->request()->route->getAttributes();

        /** @var string $heandler */
        $heandler = $this->request()->route->getHandler();

        return $this->call($heandler, $attributes);
    }
}
