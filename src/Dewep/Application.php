<?php

namespace Dewep;

use Dewep\Handlers\Error;
use Dewep\Http\Request;
use Dewep\Http\Response;
use Dewep\Middleware\Builder as MB;

/**
 * Class Application
 *
 * @package Dewep
 */
class Application
{
    /** @var array */
    protected static $allowHeaders = [
        'Content-Type' => null,
    ];

    /**
     * Application constructor.
     */
    public function __construct()
    {
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
     * @throws \Exception
     */
    public function bootstrap()
    {
        ob_start();

        /**
         * routes
         */
        $routes = Config::get('routes', []);
        if (is_string($routes)) {
            $routes = call_user_func($routes, $this);
        }

        /**
         * request
         */
        $request = Request::bootstrap($routes);
        Container::set('request', $request);

        /**
         * response
         */
        $response = Response::bootstrap();
        Container::set('response', $response);

        /**
         * middleware
         */
        $middleware = Config::get('middleware', []);

        MB::makes($middleware['before'] ?? [], $request, $response, 'before');

        /** @var Response $content */
        $content = $this->getApplication($request, $response);
        if ($content instanceof Response) {
            $response = $content;
        } else {
            $response = $response->setBody($content, Config::get('response'));
        }

        MB::makes($middleware['after'] ?? [], $request, $response, 'after');

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
     * @param Request  $request
     * @param Response $response
     *
     * @return mixed
     * @throws \Exception
     */
    private function getApplication(Request $request, Response $response)
    {
        /** @var array $attributes */
        $attributes = $request->route->getAttributes();

        /** @var string $heandler */
        $heandler = $request->route->getHandler();

        list($class, $method) = explode('::', $heandler, 2);

        $object = new $class($request, $response);

        return call_user_func_array([$object, $method], $attributes);
    }

}
