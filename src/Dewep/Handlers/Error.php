<?php

declare(strict_types=1);

namespace Dewep\Handlers;

use Dewep\Config;
use Dewep\Container;
use Dewep\Exception\HttpException;
use Dewep\Exception\UndefinedFormatException;
use Dewep\Http\Response;

final class Error
{
    /**
     * Loader processing errors.
     */
    public static function bootstrap(): void
    {
        set_error_handler(Error::class.'::error');
        set_exception_handler(Error::class.'::exception');

        ini_set('display_errors', '1');
        error_reporting(-1);
    }

    /**
     * @param mixed $errno
     * @param mixed $errstr
     * @param mixed $errfile
     * @param mixed $errline
     */
    public static function error($errno, $errstr, $errfile, $errline): void
    {
        if (0 < error_reporting()) {
            $errtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20);
            self::build($errno, $errstr, $errfile, $errline, $errtrace);
        }
    }

    public static function exception(\Throwable $e): void
    {
        $code = 500;

        if ($e instanceof HttpException) {
            $code = $e->getCode();
        }

        self::build(
            $e->getCode(),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTrace(),
            $code
        );
    }

    /**
     * @param mixed $no
     * @param mixed $str
     * @param mixed $file
     * @param mixed $line
     */
    private static function build(
        $no,
        $str,
        $file,
        $line,
        array $trace = [],
        int $httpCode = 500
    ): void {
        $debug = Config::get('debug', false);

        $response = [
            'errorMessage' => $str,
            'errorCode'    => $no,
        ];

        $file = explode('/', $file);
        $file = array_slice($file, count($file) - 3);
        $file = implode('/', $file);

        Container::get('logger')->error(
            sprintf('%s: %s in %s:%s', $no, $str, $file, $line),
            $trace
        );

        if ($debug) {
            $response['errorFile'] = $file.':'.$line;
        }

        try {
            HttpCodeHandler::make(
                Response::initialize()
                    ->setBody($response)
                    ->setContentType((string) Config::get('response'))
                    ->setStatusCode($httpCode)
            )->send();
        } catch (UndefinedFormatException $e) {
            echo $str;
        }

        exit(0);
    }
}
