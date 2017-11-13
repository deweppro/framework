<?php

namespace Dewep\Parsers;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Request
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
