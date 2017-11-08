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

namespace Dewep\Parsers;

/**
 * Description of Body
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Body
{

    const JSON = 'application/json';
    const XML_TEXT = 'text/xml';
    const XML_APP = 'application/xml';
    const FORM_WWW = 'application/x-www-form-urlencoded';
    const FORM_DATA = 'multipart/form-data';

    /**
     *
     * @param string $body
     * @return type
     */
    public static function json(string $body)
    {
        $body = json_decode($body, true);

        if (empty($body)) {
            $body = null;
        }

        return $body;
    }

    /**
     *
     * @param string $body
     * @return type
     */
    public static function url(string $body)
    {
        $body = rawurldecode($body);

        $result = [];

        parse_str($body, $result);

        if (empty($result)) {
            $result = null;
        }

        return $result;
    }

    /**
     *
     * @param string $body
     * @return type
     */
    public static function xml(string $body)
    {
        $backup = libxml_disable_entity_loader(true);
        $backup_errors = libxml_use_internal_errors(true);
        $body = simplexml_load_string($body);
        libxml_disable_entity_loader($backup);
        libxml_clear_errors();
        libxml_use_internal_errors($backup_errors);


        if (empty($body)) {
            $body = null;
        }

        return $body;
    }

    /**
     *
     * @param type $body
     * @return type
     */
    public static function other($body)
    {
        return empty($body) ? null : $body;
    }

}
