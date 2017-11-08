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

use Psr\Log\LoggerInterface;
use Dewep\Patterns\Singleton;
use Dewep\Exception\ArgumentExeption;

/**
 * Description of Log
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Logger extends Singleton implements LoggerInterface
{

    private $path;
    private $template = "{date}\t[{level}]\t{path}\t{message}\t{context}";

    public function setLogFile(string $path, string $template = null)
    {
        if (
                is_dir($path) ||
                !is_writable($path)
        ) {
            throw new ArgumentExeption("Cannot write log to file - {$path}");
        }
        $this->path = $path;
        if (!is_null($template)) {
            $this->template = $template;
        }
    }

    private function write($level, $message, array $context = array())
    {
        if (is_string($message)) {
            //--
        } else if (
                is_object($message) &&
                method_exists($message, '__toString')
        ) {
            //--
        } else {
            throw new ArgumentExeption('Passed an unsupported message type in the log.');
        }

        if (!empty($context) && !empty($message)) {
            $replace = array();
            foreach ($context as $key => $val) {
                if (!is_array($val) && (!is_object($val) || method_exists($val,
                                '__toString'))) {
                    $replace['{' . $key . '}'] = $val;
                }
            }

            $message = strtr($message, $replace);
        }

        $debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $log = strtr($this->template,
                [
            '{date}' => date('c'),
            '{level}' => $level,
            '{path}' => $debug[1] ?? '-',
            '{message}' => $level,
            '{context}' => json_encode($context, JSON_UNESCAPED_UNICODE),
                ]
        );

        $logFile = $this->path ?? sys_get_temp_dir() . '/dewep.log';

        file_put_contents($logFile, $log . "\n", FILE_APPEND);
    }

    public function alert($message, array $context = array())
    {
        $this->write(__FUNCTION__, $message, $context);
    }

    public function critical($message, array $context = array())
    {
        $this->write(__FUNCTION__, $message, $context);
    }

    public function debug($message, array $context = array())
    {
        $this->write(__FUNCTION__, $message, $context);
    }

    public function emergency($message, array $context = array())
    {
        $this->write(__FUNCTION__, $message, $context);
    }

    public function error($message, array $context = array())
    {
        $this->write(__FUNCTION__, $message, $context);
    }

    public function info($message, array $context = array())
    {
        $this->write(__FUNCTION__, $message, $context);
    }

    public function log($level, $message, array $context = array())
    {
        $this->write($level, $message, $context);
    }

    public function notice($message, array $context = array())
    {
        $this->write(__FUNCTION__, $message, $context);
    }

    public function warning($message, array $context = array())
    {
        $this->write(__FUNCTION__, $message, $context);
    }

    public static function jot()
    {
        $log = self::getInstance();
        $args = func_get_args();
        $log->write('*', '', $args);
    }

}
