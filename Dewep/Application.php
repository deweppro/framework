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

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Application
{

    public function __construct(string $configFilePath)
    {
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

    public function bootstrap()
    {
        $response = Response::bootstrap();
        Container::exist('response', $response);

        $request = Request::bootstrap();
        Container::exist('request', $request);

        $middleware = Config::get('middleware', []);

        if (!empty($middleware['request'])) {
            MB::makes($middleware['request'], $request, $response);
        }

        $attributes = $request->getAttributes();
        $heandler = $request->route->getHandler();

        list($class, $method) = explode('::', $heandler);

        $object = new $class($request, $response);
        $content = call_user_func_array([$object, $method], $attributes);

        $response->setBody($content);

        if (!empty($middleware['response'])) {
            MB::makes($middleware['response'], $request, $response);
        }

        echo $response;
    }

}
