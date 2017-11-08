<?php

namespace Dewep\Http;

use Dewep\Interfaces\ActionIntrface;

/**
 * Description of Headers
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Headers implements ActionIntrface
{

    use \Dewep\http\Traits\Http;

    /**
     * REQEST
     */
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

    /**
     *
     */
    protected $headers;
    protected $serverParams;
    protected $cookies;

    /**
     *
     */
    public function __construct()
    {
        $headers = array_filter($_SERVER,
                function($k) {
            return substr($k, 0, 5) == 'HTTP_';
        }, ARRAY_FILTER_USE_KEY);

        $serverParams = array_diff_key($_SERVER, $headers);

        $this->serverParams = array_map([$this, 'originalKey'], $serverParams);
        $this->cookies = array_map([$this, 'originalKey'], $_COOKIE);

        foreach ($headers as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     *
     * @return type
     */
    public function all()
    {
        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[$this->originalKey($key)] = $value;
        }
        return $headers;
    }

    /**
     *
     * @param type $key
     * @param type $value
     */
    public function set($key, $value)
    {
        $this->headers[$this->normalizeKey($key)] = $value;
    }

    /**
     *
     * @param type $key
     * @return type
     */
    public function has($key)
    {
        $key = $this->normalizeKey($key);
        return isset($this->headers[$key]);
    }

    /**
     *
     * @param type $key
     * @param type $default
     * @return type
     */
    public function get($key, $default = null)
    {
        $key = $this->normalizeKey($key);
        return $this->headers[$key] ?? $default;
    }

    /**
     *
     * @param type $key
     * @param type $value
     */
    public function add($key, $value)
    {
        $key = $this->normalizeKey($key);
        $valueExist = $this->get($key, []);

        $valueExist = !is_array($valueExist) ? [$valueExist] : $valueExist;
        $value = !is_array($value) ? [$value] : $value;

        $this->set($key, array_merge($valueExist, $value));
    }

    /**
     *
     * @param type $key
     */
    public function remove($key)
    {
        $key = $this->normalizeKey($key);
        unset($this->headers[$key]);
    }

    /*
     *
     */

    private function contentType()
    {
        $contentType = $this->headers['content-type'][0] ??
                $this->headers['content-type'] ??
                '';
        $contentTypeArray = explode(';', strtolower($contentType), 2);
        $contentTypeArray = array_map('trim', $contentTypeArray);
        return $contentTypeArray;
    }

    public function getContentType()
    {
        return $this->contentType()[0] ?? '';
    }

    public function getContentTypeParams()
    {
        $params = $this->contentType()[1] ?? '';
        return explode('=', $params, 2);
    }

    /*
     *
     */

    public function getServerParams()
    {
        return $this->serverParams;
    }

    public function getServerParam(string $key, $default = null)
    {
        $key = $this->originalKey($key);
        return $this->serverParams[$key] ?? $default;
    }

    /*
     *
     */

    public function getCookies()
    {
        return $this->cookies;
    }

    public function setCookies($key, $value, $expire = 3600, $path = '/',
            $domain = '*', $secure = false, $httponly = false)
    {
        $key = $this->originalKey($key);
        $this->cookies[$key] = $value;
        setcookie($key, $value, $expire, $path, $domain, $secure, $httponly);
    }

    public function removeCookies($key)
    {
        $key = $this->originalKey($key);
        unset($this->cookies[$key]);
        setcookie($key, '', time() - 1);
    }

}
