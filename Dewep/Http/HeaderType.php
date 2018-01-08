<?php


namespace Dewep\Http;

/**
 * Description of HeaderType
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class HeaderType
{

    // with HTTP_
    const ACCEPT_LANGUAGE = 'Accept-Language';
    const ACCEPT_ENCODING = 'Accept-Encoding';
    const REFERER = 'Referer';
    const USER_AGENT = 'User-Agent';
    const CONNECTION = 'Connection';
    const HOST = 'Host';
    // without HTTP_
    const REQUEST_SCHEME = 'Request-Scheme';
    const SERVER_PROTOCOL = 'Server-Protocol';
    const DOCUMENT_ROOT = 'Document-Root';
    const DOCUMENT_URI = 'Document-Uri';
    const REQUEST_URI = 'Request-Uri';
    const SCRIPT_NAME = 'Script-Name';
    const CONTENT_LENGTH = 'Content-Length';
    const CONTENT_TYPE = 'Content-Type';
    const REQUEST_METHOD = 'Request-Method';
    const QUERY_STRING = 'Query-String';
    const REQUEST_TIME = 'Request-Time';
    const SERVER_NAME = 'Server-Name';

}
