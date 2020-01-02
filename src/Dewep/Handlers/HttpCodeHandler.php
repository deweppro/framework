<?php

declare(strict_types=1);

namespace Dewep\Handlers;

use Dewep\Application;
use Dewep\Config;
use Dewep\Http\Response;

final class HttpCodeHandler
{
    /** @var array */
    private static $handlers = [];

    public static function setHandler(int $code, callable $handler): void
    {
        self::$handlers[$code] = $handler;
    }

    public static function setHandlers(array $data): void
    {
        foreach ($data as $code => $handler) {
            self::$handlers[$code] = $handler;
        }
    }

    public static function make(Response $response): Response
    {
        if (!isset(self::$handlers[$response->getStatusCode()])) {
            return $response;
        }

        $content = call_user_func(
            self::$handlers[$response->getStatusCode()],
            $response->getBody()
        );

        return $response->setBody($content)
            ->setContentType(
                is_scalar($content) ?
                    Application::DEFAULT_CONTENT_TYPE :
                    (string)Config::get('response')
            );
    }
}
