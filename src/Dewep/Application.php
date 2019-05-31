<?php declare(strict_types=1);

namespace Dewep;

use Dewep\Exception\RuntimeException;
use Dewep\Handlers\Error;
use Dewep\Http\Request;
use Dewep\Http\Response;
use Dewep\Interfaces\ApplicationInterface;

/**
 * Class Application
 *
 * @package Dewep
 */
class Application implements ApplicationInterface
{
    /**
     * @var array
     */
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
            'Access-Control-Allow-Origin'      => Config::get('domain', '*'),
            'Access-Control-Allow-Methods'     => 'HEAD,OPTIONS,GET,POST,PUT,DELETE,TRACE',
            'Access-Control-Max-Age'           => 0,
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Headers'     => implode(
                ', ',
                array_keys(
                    array_replace(
                        array_flip($allowHeaders),
                        static::$allowHeaders
                    )
                )
            ),
            'Cache-Control'                    => 'no-cache',
            'Pragma'                           => 'no-cache',
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
            $routes = Builder::make($routes);
            if (!is_array($routes)) {
                throw new RuntimeException('The error handling routing.');
            }
        }

        /**
         *  Build Request+Response
         */
        $request = Request::bootstrap($routes);
        $response = Response::bootstrap();

        Container::set('request', $request);
        Container::set('response', $response);

        /**
         * middleware
         */
        $middleware = Config::get('middleware', []);

        Builder::makes($middleware['before'] ?? [], 'before');

        /**
         * call controllers
         */
        $content = $this->router($request);
        if ($content instanceof Response) {
            Container::set('response', $content);
        } else {
            $response->setBody($content, Config::get('response'));
        }

        Builder::makes($middleware['after'] ?? [], 'after');

        $err = ob_get_contents();
        ob_end_flush();

        /**
         * errors
         */
        if (!empty($err)) {
            Container::get('logger')->warning($err);
        }

        echo Container::get('response');
    }

    /**
     * @param \Dewep\Http\Request $request
     *
     * @return mixed
     * @throws \Exception
     */
    protected function router(Request $request)
    {
        /** @var array $attributes */
        $attributes = $request->route->getAttributes();

        /** @var string $handler */
        $handler = $request->route->getHandler();

        return Builder::call($handler, $attributes);
    }
}
