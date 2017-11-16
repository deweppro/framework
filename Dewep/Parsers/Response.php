<?php

namespace Dewep\Parsers;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Response
{

    /**
     * Типы контента
     */
    const TYPE_JSON = 'json';
    const TYPE_XML = 'xml';
    const TYPE_HTML = 'html';

    /**
     * HTTP заголовки
     */
    const HTTP_JSON = 'application/json; charset=UTF-8';
    const HTTP_XML = 'application/xml; charset=UTF-8';
    const HTTP_HTML = 'text/html; charset=UTF-8';

    /**
     *
     * @param array $body
     * @return string
     */
    public static function json(array $body): string
    {
        return json_encode($body, JSON_UNESCAPED_UNICODE);
    }

    /**
     *
     * @param array $body
     * @return string
     */
    public static function xml(array $body, string $root = '<root/>'): string
    {
        $xml = new \SimpleXMLElement($root);
        array_walk_recursive($body, array($xml, 'addChild'));
        return $xml->asXML();
    }

    /**
     *
     * @param array $body
     * @return string
     */
    public static function html(array $body): string
    {
        $xml = self::xml($body, '<html/>');
        $doc = new \DOMDocument(5, 'UTF-8');
        $doc->loadXML($xml);
        return $doc->saveHTML();
    }

    public static function other($body)
    {
        return $body;
    }

}
