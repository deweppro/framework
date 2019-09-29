<?php declare(strict_types=1);

namespace Dewep\Handlers;

use Dewep\Http\Response;

class HttpCodeHandler
{
    protected static $handlers = [];

    /**
     * @param int      $code
     * @param callable $handler
     */
    public static function setHandler(int $code, callable $handler): void
    {
        self::$handlers[$code] = $handler;
    }

    /**
     * @param array $data
     */
    public static function setHandlers(array $data): void
    {
        foreach ($data as $code => $handler) {
            self::$handlers[$code] = $handler;
        }
    }

    /**
     * @param \Dewep\Http\Response $response
     *
     * @return \Dewep\Http\Response
     */
    public static function make(Response $response): Response
    {
        if (!isset(self::$handlers[$response->getStatusCode()])) {
            return $response;
        }

        $handler = self::$handlers[$response->getStatusCode()];

        return $response->setBody(
            call_user_func($handler, $response->getBody())
        );
    }
}
