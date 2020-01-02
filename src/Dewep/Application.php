<?php

declare(strict_types=1);

namespace Dewep;

use Dewep\Exception\RuntimeException;
use Dewep\Handlers\Error;
use Dewep\Handlers\HttpCodeHandler;
use Dewep\Http\HeaderBag;
use Dewep\Http\HeaderTypeBag;
use Dewep\Http\Request;
use Dewep\Http\Response;
use Dewep\Http\SessionBag;
use Dewep\Interfaces\ApplicationInterface;

final class Application implements ApplicationInterface
{
    public const DEFAULT_CONTENT_TYPE = 'text/html; charset=utf-8';

    public function __construct()
    {
        Error::bootstrap();
        HttpCodeHandler::setHandlers(Config::get('codes', []));
    }

    /**
     * @throws \Dewep\Exception\HttpException
     * @throws \Dewep\Exception\RuntimeException
     * @throws \Dewep\Exception\UndefinedFormatException
     */
    public function bootstrap(): void
    {
        ob_start();

        /**
         *  Build Request.
         */
        $request = Request::initialize();
        Container::set('request', $request);

        // fix
        Config::exist('domain', $request->getHeader()->getHost());
        Config::exist('response', $request->getHeader()->getAcceptType());

        /**
         * Build Response.
         */
        $response = new Response(
            new HeaderBag($this->cors()),
            $request->getCookie()
        );
        Container::set('response', $response);

        if (false === $request->getServer()->isOptions()) {

            /**
             * session.
             */
            $session = Config::get('session', []);

            $request->setSession(
                SessionBag::initialize(
                    $session['_'] ?? null,
                    (int) ($session['lifetime'] ?? 3600),
                    (string) Config::get('domain', '')
                )
            );

            /**
             * routes.
             */
            $routes = Config::get('routes', []);
            if (is_string($routes)) {
                $routes = Builder::make($routes);
                if (!is_array($routes)) {
                    throw new RuntimeException('The error handling routing.');
                }
            }
            $request->getRoute()->replace($routes)->bind();

            /**
             * middleware.
             */
            $middleware = Config::get('middleware', []);

            $this->middleware(
                $request,
                $response,
                $middleware['before'] ?? [],
                'before'
            );

            /**
             * call controllers.
             */
            $content = $this->router($request);
            if ($content instanceof Response) {
                Container::set('response', $content);
            } else {
                $response->setContentType(
                    is_scalar($content) ?
                        self::DEFAULT_CONTENT_TYPE :
                        (string) Config::get('response')
                );
                $response->setBody($content);
            }

            $this->middleware(
                $request,
                $response,
                $middleware['after'] ?? [],
                'after'
            );

            /**
             * errors.
             */
            $err = ob_get_contents();
            if (!empty($err)) {
                Container::get('logger')->warning($err);
            }
        }

        ob_end_clean();

        HttpCodeHandler::make(Container::get('response'))->send();
        exit(0);
    }

    private function cors(): array
    {
        $allowHeaders   = Config::get('allowHeaders', []);
        $defaultHeaders = [
            HeaderTypeBag::CONTENT_TYPE,
            HeaderTypeBag::ACCEPT_TYPE,
        ];

        return [
            'Access-Control-Allow-Origin'      => Config::get('domain', '*'),
            'Access-Control-Allow-Methods'     => 'HEAD,OPTIONS,GET,POST,PUT,DELETE,TRACE',
            'Access-Control-Max-Age'           => 0,
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Headers'     => implode(
                ', ',
                array_keys(
                    array_flip(
                        array_merge(
                            $defaultHeaders,
                            $allowHeaders
                        )
                    )
                )
            ),
            'Cache-Control' => 'no-cache',
            'Pragma'        => 'no-cache',
        ];
    }

    private function middleware(
        Request $request,
        Response $response,
        array $middlewares,
        string $handler
    ): void {
        foreach ($middlewares as $name => $params) {
            $class = $params['_'] ?? null;
            unset($params['_']);

            if (!empty($class) && is_string($class)) {
                Builder::make($class, $handler, [$request, $response, $params]);
            } else {
                Container::get('logger')->warning(
                    sprintf(
                        'Bad middleware in %s:%s',
                        $name,
                        $handler
                    )
                );
            }
        }
    }

    /**
     * @throws \Dewep\Exception\HttpException
     *
     * @return mixed
     */
    private function router(Request $request)
    {
        /** @var array $attributes */
        $attributes = $request->getRoute()->getAttributes();

        /** @var string $handler */
        $handler = $request->getRoute()->getHandler();

        array_unshift($attributes, $request);

        return Builder::make($handler, null, $attributes);
    }
}
