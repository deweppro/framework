<?php

namespace Dewep;

use Dewep\Http\Request;
use Dewep\Http\Response;
use Dewep\Handlers\Error;
use Dewep\Middleware\Builder as MB;
use Dewep\Exception\RuntimeException;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Application
{

    /**
     * Application constructor.
     * @param string $configFilePath
     * @throws Exception\FileException
     * @throws RuntimeException
     */
    public function __construct(string $configFilePath)
    {
        Config::makeSysFolders();

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
     * @throws Exception\HttpException
     * @throws Patterns\RuntimeException
     */
    public function bootstrap()
    {
        ob_start();

        $response = Response::bootstrap();
        Container::set('response', $response);

        $request = Request::bootstrap();
        Container::set('request', $request);

        $middleware = Config::get('middleware', []);

        MB::makes($middleware['request'] ?? [], $request, $response);

        $content = $this->getApplication($request, $response);
        $response = $response->setBody($content);

        MB::makes($middleware['response'] ?? [], $request, $response);

        $err = ob_get_contents();
        ob_end_flush();

        if (!empty($err)) {
            Container::get('logger')->warning($err);
        }

        echo $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @throws Exception\HttpException
     */
    private function getApplication(Request $request, Response $response)
    {
        $attributes = $request->getAttributes();
        $heandler = $request->route->getHandler();

        list($class, $method) = explode('::', $heandler, 2);

        $object = new $class($request, $response);
        return call_user_func_array([$object, $method], $attributes);
    }

}
