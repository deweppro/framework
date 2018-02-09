<?php

namespace Dewep;

use Dewep\Exception\RuntimeException;
use Dewep\Handlers\Error;
use Dewep\Http\Request;
use Dewep\Http\Response;
use Dewep\Middleware\Builder as MB;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Application
{
    protected static $allowHeaders = [
        'User-Agent'        => null,
        'X-Requested-With'  => null,
        'If-Modified-Since' => null,
        'Cache-Control'     => null,
        'Content-Type'      => null,
        'Range'             => null,
    ];

    /**
     * @param string $configFilePath
     * @throws Exception\FileException
     * @throws RuntimeException
     */
    public function __construct(string $configFilePath)
    {
        //Config::makeSysFolders();

        if (
            !file_exists($configFilePath) ||
            !is_readable($configFilePath)
        ) {
            throw new RuntimeException('Config file not found!');
        }
        Config::fromYaml($configFilePath);

        Error::bootstrap();
    }

    /**
     * @param array $allowHeaders
     */
    public static function fixOptionsRequest(array $allowHeaders = [])
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

            $headers = [
                'Access-Control-Allow-Origin'      => Config::get('domain', '*'),
                'Access-Control-Allow-Methods'     => 'GET, POST, PUT, DELETE, OPTIONS',
                'Access-Control-Max-Age'           => 0,
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Allow-Headers'     => implode(
                    ', ',
                    array_replace(
                        array_values($allowHeaders),
                        static::allowHeaders
                    )
                ),
                'Cache-Control'                    => 'no-cache',
                'Pragma'                           => 'no-cache',
            ];

            foreach ($headers as $key => $value) {
                header(sprintf('%s: %s', $key, $value), true);
            }

            http_send_status(204);

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
         * response
         */
        $response = Response::bootstrap();
        Container::set('response', $response);

        /**
         * request
         */
        $request = Request::bootstrap(Config::get('routes', []));
        Container::set('request', $request);

        /**
         * middleware
         */
        $middleware = Config::get('middleware', []);

        MB::makes($middleware['request'] ?? [], $request, $response, 'requestAction');

        /** @var Response $content */
        $content = $this->getApplication($request, $response);
        if ($content instanceof Response) {
            $response = $content;
        } else {
            $response = $response->setBody($content, Config::get('response'));
        }

        MB::makes($middleware['response'] ?? [], $request, $response, 'responseAction');

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
     * @param Request $request
     * @param Response $response
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
