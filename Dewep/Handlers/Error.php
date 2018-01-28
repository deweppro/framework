<?php

namespace Dewep\Handlers;

use Dewep\Config;
use Dewep\Container;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Error
{
    /**
     * Loader processing errors
     */
    public static function bootstrap()
    {
        set_error_handler('\\Dewep\\Handlers\\Error::error');
        set_exception_handler('\\Dewep\\Handlers\\Error::exception');

        ini_set('display_errors', 1);
        error_reporting(-1);
    }

    /**
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     */
    public static function error($errno, $errstr, $errfile, $errline)
    {
        if (0 == error_reporting()) {
            return false;
        }
        $errtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20);

        return self::build($errno, $errstr, $errfile, $errline, $errtrace);
    }

    /**
     * @param $no
     * @param $str
     * @param $file
     * @param $line
     * @param array $trace
     */
    private static function build($no, $str, $file, $line, $trace = [])
    {
        $debug = Config::get('debug', false);

        $response = [
            'errorMessage' => $str,
            'errorCode' => $no,
        ];

        $file = explode('/', $file);
        $file = array_slice($file, count($file) - 3);
        $file = implode('/', $file);

        Container::get('logger')->error("{$no}: {$str} in {$file}:{$line}", $trace);

        if ($debug) {
            $response['errorFile'] = $file.':'.$line;
        }

        echo Container::get('response')->setBody($response, Config::get('response'));
        die;
    }

    /**
     * @param \Throwable $e
     */
    public static function exception(\Throwable $e)
    {
        return self::build(
            $e->getCode(),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTrace()
        );
    }

}
