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

namespace Dewep\Http;

/**
 * Description of HeaderType
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class HeaderType
{

    // with HTTP_
    const ACCEPT_LANGUAGE = 'ACCEPT_LANGUAGE';
    const ACCEPT_ENCODING = 'ACCEPT_ENCODING';
    const REFERER = 'REFERER';
    const USER_AGENT = 'USER_AGENT';
    const CONNECTION = 'CONNECTION';
    const HOST = 'HOST';
    // without HTTP_
    const REQUEST_SCHEME = 'REQUEST_SCHEME';
    const SERVER_PROTOCOL = 'SERVER_PROTOCOL';
    const DOCUMENT_ROOT = 'DOCUMENT_ROOT';
    const DOCUMENT_URI = 'DOCUMENT_URI';
    const REQUEST_URI = 'REQUEST_URI';
    const SCRIPT_NAME = 'SCRIPT_NAME';
    const CONTENT_LENGTH = 'CONTENT_LENGTH';
    const CONTENT_TYPE = 'CONTENT_TYPE';
    const REQUEST_METHOD = 'REQUEST_METHOD';
    const QUERY_STRING = 'QUERY_STRING';
    const REQUEST_TIME = 'REQUEST_TIME';

}
