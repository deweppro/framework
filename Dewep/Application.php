<?php

/*
 * The MIT License
 *
 * Copyright 2017 Mikhail Knyazhev <markus621@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Dewep;

use Dewep\Http\Request;
use Dewep\Http\Response;
use Dewep\Handlers\Error;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Dewep\Middleware\Builder as MB;
use Dewep\Exception\RuntimeException;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Application
{

    /**
     *
     * @param string $configFilePath
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

        Container::exist('logger',
                function() {
            $debug = Config::get('debug', false);
            $logfile = Config::dirTemp() . '/app.log';

            $logger = new Logger('app');
            $logger->pushHandler(new StreamHandler($logfile,
                    $debug ? Logger::DEBUG : Logger::INFO));

            return $logger;
        });

        Error::bootstrap();
    }

    /**
     *
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
        $response->setBody($content);

        MB::makes($middleware['response'] ?? [], $request, $response);

        $err = ob_get_contents();
        ob_end_flush();

        if (!empty($err)) {
            Container::get('logger')->warning($err);
        }

        echo $response;
    }

    private function getApplication(Request $request, Response $response)
    {
        $attributes = $request->getAttributes();
        $heandler = $request->route->getHandler();

        list($class, $method) = explode('::', $heandler, 2);

        $object = new $class($request, $response);
        return call_user_func_array([$object, $method], $attributes);
    }

}
