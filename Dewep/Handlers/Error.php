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

namespace Dewep\Handlers;

use Dewep\Container;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Description of Error
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Error
{

    /**
     *
     */
    public static function bootstrap()
    {
        set_error_handler('\\Dewep\\Handlers\\Error::error');
        set_exception_handler('\\Dewep\\Handlers\\Error::exception');

        ini_set('display_errors', 1);
        error_reporting(-1);
    }

    /**
     *
     * @param type $errno
     * @param type $errstr
     * @param type $errfile
     * @param type $errline
     * @return type
     */
    public static function error($errno, $errstr, $errfile, $errline)
    {
        if (0 == error_reporting()) {
            return;
        }
        $errtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20);

        return self::build($errno, $errstr, $errfile, $errline, $errtrace);
    }

    /**
     *
     * @param \Throwable $e
     * @return type
     */
    public static function exception(\Throwable $e)
    {
        return self::build($e->getCode(), $e->getMessage(), $e->getFile(),
                        $e->getLine(), $e->getTrace());
    }

    /**
     *
     * @param type $no
     * @param type $str
     * @param type $file
     * @param type $line
     * @param type $trace
     */
    private static function build($no, $str, $file, $line, $trace = [])
    {
        $file = explode('/', $file);
        $file = array_slice($file, count($file) - 3);
        $file = implode('/', $file);

        Container::get('logger')->error("{$no}: {$str} in {$file}:{$line}",
                $trace);

        echo Container::get('response')->setBody([
            'errorMessage' => $str,
            'errorFile' => $file . ':' . $line,
            'errorCode' => $no
        ]);

        die;
    }

}
