<?php

namespace Dewep\Http;

use Psr\Http\Message\UriInterface;

/**
 * Description of Uri
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Uri implements UriInterface
{

    protected $scheme = '';
    protected $user = '';
    protected $password = '';
    protected $host = '';
    protected $port;
    protected $basePath = '';
    protected $path = '';
    protected $query = '';
    protected $fragment = '';

    public static function init(string $uri = null)
    {
        if (empty($uri)) {
            $scheme = (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') ? 'http' : 'https';
            $user = $_SERVER['PHP_AUTH_USER'] ?? '';
            $pass = $_SERVER['PHP_AUTH_PW'] ?? '';
            $host = $_SERVER['HTTP_HOST'] ?? null;
            $port = $_SERVER['SERVER_PORT'] ?? null;
            $path = $_SERVER['REQUEST_URI'] ?? '/';
            $query = $_SERVER['QUERY_STRING'] ?? '';
            $fragment = '';
        } else {
            $parts = parse_url($uri);
            $scheme = $parts['scheme'] ?? '';
            $user = $parts['user'] ?? '';
            $pass = $parts['pass'] ?? '';
            $host = $parts['host'] ?? '';
            $port = $parts['port'] ?? null;
            $path = $parts['path'] ?? '/';
            $query = $parts['query'] ?? '';
            $fragment = $parts['fragment'] ?? '';
        }

        return new static($scheme, $host, $port, $path, $query, $fragment,
                $user, $pass);
    }

    public function __construct($scheme, $host, $port = null, $path = null,
            $query = '', $fragment = '', $user = '', $password = '')
    {
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = intval($port);
        $this->path = $path ?? '/';
        $this->query = $query;
        $this->fragment = $fragment;
        $this->user = $user;
        $this->password = $password;
    }

    public function __toString()
    {
        $scheme = $this->getScheme();
        $authority = $this->getAuthority();
        $path = $this->getPath();
        $query = $this->getQuery();
        $fragment = $this->getFragment();

        return ($scheme ? $scheme . ':' : '')
                . ($authority ? '//' . $authority : '')
                . '/' . trim($path, '/')
                . ($query ? '?' . $query : '')
                . ($fragment ? '#' . $fragment : '');
    }

    public function getAuthority()
    {
        $userInfo = $this->getUserInfo();
        $host = $this->getHost();
        $port = $this->getPort();
        return ($userInfo ? $userInfo . '@' : '') . $host . ($port !== null ? ':' . $port : '');
    }

    /*
     *
     */

    public function getScheme()
    {
        return $this->scheme;
    }

    public function withScheme($scheme)
    {
        $clone = clone $this;
        $clone->scheme = $scheme;
        return $clone;
    }

    /*
     *
     */

    public function getHost()
    {
        return $this->host;
    }

    public function withHost($host)
    {
        $clone = clone $this;
        $clone->host = $host;
        return $clone;
    }

    /*
     *
     */

    public function getPath()
    {
        return $this->path;
    }

    public function withPath($path)
    {
        $clone = clone $this;
        $clone->path = $path;
        return $clone;
    }

    /*
     *
     */

    public function getPort()
    {
        return $this->port;
    }

    public function withPort($port)
    {
        $clone = clone $this;
        $clone->port = $port;
        return $clone;
    }

    /*
     *
     */

    public function getQuery()
    {
        return $this->query;
    }

    public function withQuery($query)
    {
        $clone = clone $this;
        $clone->query = $query;
        return $clone;
    }

    /*
     *
     */

    public function getFragment()
    {
        return $this->fragment;
    }

    public function withFragment($fragment)
    {
        $clone = clone $this;
        $clone->fragment = trim($fragment, '#');
        return $clone;
    }

    /*
     *
     */

    public function getUserInfo()
    {
        return $this->user . ($this->password ? ':' . $this->password : '');
    }

    public function withUserInfo($user, $password = null)
    {
        $clone = clone $this;
        $clone->user = $user;
        $clone->password = $password ? $password : '';
        return $clone;
    }

}
