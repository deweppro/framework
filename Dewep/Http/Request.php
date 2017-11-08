<?php

namespace Dewep\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Dewep\Error\Exeption;
use Dewep\Parsers\Body as BodyParser;

/**
 * Description of Request
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Request extends Message implements ServerRequestInterface
{

    protected $validMethods = [
        'CONNECT', 'DELETE', 'GET',
        'HEAD', 'OPTIONS', 'PATCH',
        'POST', 'PUT', 'TRACE',
    ];
    protected $url;
    protected $route;
    protected $uploadedFiles;
    protected $bodyParsers;
    protected $bodyParsed = false;

    public static function init(array $config)
    {
        $url = Uri::init();
        $headers = new Headers();
        $route = new Route($config['routes'], $headers);
        $body = Stream::init();
        $uploadedFiles = UploadedFile::init();

        return new static($url, $route, $headers, $body, $uploadedFiles);
    }

    /*
     *
     */

    public function __construct(UriInterface $url, Route $route,
            Headers $headers, StreamInterface $body, array $uploadedFiles)
    {
        $this->url = $url;
        $this->route = &$route;
        $this->headers = &$headers;
        $this->body = &$body;
        //--
        $this->uploadedFiles = &$uploadedFiles;
    }

    /*
     *
     */

    public function getMethod()
    {
        return $this->headers->get(Headers::REQUEST_METHOD);
    }

    public function withMethod($method)
    {
        $method = strtoupper($method);
        if (!in_array($method, $this->validMethods)) {
            Exeption::error(0, 'Sent is not the standard method.');
        }
        $clone = clone $this;
        $clone->headers->set(Headers::REQUEST_METHOD, $method);
        return $clone;
    }

    /*
     *
     */

    public function getRequestTarget()
    {
        $path = $this->uri->getPath();
        $path = '/' . trim($path, '/');
        $query = $this->uri->getQuery();
        if (!empty($query)) {
            $path .= '?' . $query;
        }
        return $path;
    }

    public function withRequestTarget($requestTarget)
    {
        $requestTarget = strtr($requestTarget, ' ', '');
        @list($path, $query) = explode('?', $requestTarget, 2);

        $clone = clone $this;


        $clone->url = $clone->url->withPath($path);
        if (!empty($query)) {
            $clone->url = $clone->url->withQuery($query);
        }
        return $clone;
    }

    /*
     *
     */

    public function getUri()
    {
        return $this->uri;
    }

    public function withUri(\Psr\Http\Message\UriInterface $uri,
            $preserveHost = false)
    {
        $clone = clone $this;
        $clone->uri = $uri;

        if (!$preserveHost) {
            if (!empty($uri->getHost())) {
                $clone->headers->set('Host', $uri->getHost());
            }
        } else {
            if (
                    !empty($uri->getHost()) &&
                    (
                    !$this->hasHeader('Host') ||
                    empty($this->getHeaderLine('Host'))
                    )
            ) {
                $clone->headers->set('Host', $this->getHost());
            }
        }
        return $clone;
    }

    /*
     *
     */

    public function getAttribute($name, $default = null)
    {
        return $this->route->getAttribute($name, $default);
    }

    public function getAttributes()
    {
        return $this->route->getAttributes();
    }

    public function withAttribute($name, $value)
    {
        $clone = clone $this;
        $clone->route->setAttribute($name, $value);
        return $clone;
    }

    public function withoutAttribute($name)
    {
        $clone = clone $this;
        $clone->route->removeAttribute($name);
        return $clone;
    }

    /*
     *
     */

    public function getCookieParams()
    {
        return $this->headers->getCookies();
    }

    public function withCookieParams(array $cookies)
    {
        $clone = clone $this;
        foreach ($cookies as $key => $value) {
            $clone->route->setCookies($key, $value);
        }
        return $clone;
    }

    /*
     *
     */

    public function getParsedBody()
    {
        if ($this->bodyParsed === false) {
            $contentType = $this->headers->getContentType();
            $handler = $this->bodyParsers[$contentType] ?? $this->bodyParsers['*'];

            if ($contentType == BodyParser::FORM_WWW) {
                $this->bodyParsed = $_POST;
            } else {

                $this->bodyParsed = call_user_func_array($handler,
                        [$this->body->getContents()]);
            }
        }
        return $this->bodyParsed;
    }

    public function setParserBody($type, $function)
    {
        $this->bodyParsers[$type] = $function;
    }

    private function setDefaultParsersBody()
    {
        $this->bodyParsers[BodyParser::JSON] = '\Dewep\Parsers\Body::json';
        $this->bodyParsers[BodyParser::XML_APP] = '\Dewep\Parsers\Body::xml';
        $this->bodyParsers[BodyParser::XML_TEXT] = '\Dewep\Parsers\Body::xml';
        $this->bodyParsers[BodyParser::FORM_DATA] = '\Dewep\Parsers\Body::url';
        $this->bodyParsers[BodyParser::FORM_WWW] = '\Dewep\Parsers\Body::other';
        $this->bodyParsers['*'] = '\Dewep\Parsers\Body::other';
    }

    public function withParsedBody($data)
    {
        $clone = clone $this;
        $clone->bodyParsed = $data;
        return $clone;
    }

    /*
     *
     */

    public function getQueryParams()
    {
        return BodyParser::url($this->url->getQuery());
    }

    public function withQueryParams(array $query)
    {
        $clone = clone $this;
        $clone->url = $clone->url->withQuery(http_build_query($query));
        return $clone;
    }

    /*
     *
     */

    public function getServerParams()
    {
        return $this->headers->getServerParams();
    }

    /*
     *
     */

    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        $clone = clone $this;
        $clone->uploadedFiles = $uploadedFiles;
        return $clone;
    }

}
