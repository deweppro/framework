<?php

namespace Dewep\Http;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use Dewep\Exception\RuntimeException;

/**
 * PSR7 MessageInterface
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
abstract class Message implements MessageInterface
{

    protected $protocolVersion = '1.1';
    protected static $validProtocolVersions = ['1.0', '1.1', '2.0', '2',];

    /**
     *
     * @var \Dewep\Http\Headers
     */
    protected $headers;

    /**
     *
     * @var \Psr\Http\Message\StreamInterface
     */
    protected $body;

    /*
     * BODY
     */

    public function getBody()
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        $clone = clone $this;
        $clone->body = $body;
        return $clone;
    }

    /*
     * HEADERS
     */

    public function getHeader($name)
    {
        return $this->headers->get($name);
    }

    public function getHeaderLine($name)
    {
        $value = $this->headers->get($name, []);
        return is_array($value) ? implode(',', $value) : $value;
    }

    public function getHeaders()
    {
        return $this->headers->all();
    }

    public function hasHeader($name)
    {
        return $this->headers->has($name);
    }

    public function withAddedHeader($name, $value)
    {
        $clone = clone $this;
        $clone->headers->add($name, $value);
        return $clone;
    }

    public function withHeader($name, $value)
    {
        $clone = clone $this;
        $clone->headers->set($name, $value);
        return $clone;
    }

    public function withoutHeader($name)
    {
        $clone = clone $this;
        $clone->headers->remove($name);
        return $clone;
    }

    /*
     * PROTOCOL VERSION
     */

    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion($version)
    {
        if (!in_array($version, self::$validProtocolVersions)) {
            throw new RuntimeException('Invalid HTTP version.');
        }

        $clone = clone $this;
        $clone->protocolVersion = $version;
        return $clone;
    }

}
