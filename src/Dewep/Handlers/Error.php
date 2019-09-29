<?php declare(strict_types=1);

namespace Dewep\Handlers;

use Dewep\Config;
use Dewep\Container;
use Dewep\Exception\HttpException;
use Dewep\Http\Response;

/**
 * Class Error
 *
 * @package Dewep\Handlers
 */
class Error
{
    /**
     * Loader processing errors
     */
    public static function bootstrap()
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
     *
     * @return bool
     * @throws \Exception
     */
    public static function error($errno, $errstr, $errfile, $errline)
    {
        if (0 == error_reporting()) {
            return false;
        }
        $errtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20);

        self::build($errno, $errstr, $errfile, $errline, $errtrace);
    }

    /**
     * @param mixed $no
     * @param mixed $str
     * @param mixed $file
     * @param mixed $line
     * @param array $trace
     * @param int   $httpCode
     *
     * @throws \Exception
     */
    private static function build($no, $str, $file, $line, $trace = [], int $httpCode = 500)
    {
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

        HttpCodeHandler::make(
            Response::initialize()
                ->setBody($response)
                ->setContentType(Config::get('response'))
                ->setStatusCode($httpCode)
        )->send();
        exit(0);
    }

    /**
     * @param \Throwable $e
     *
     * @throws \Exception
     */
    public static function exception(\Throwable $e)
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

}
