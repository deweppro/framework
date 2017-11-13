<?php

namespace Dewep\Http;

use Psr\Http\Message\ResponseInterface;
use Dewep\Exception\InvalidArgumentException;
use Dewep\Exception\HttpException;
use Dewep\Parsers\Response as Resp;
use Dewep\Config;

/**
 * Representation of an outgoing, server-side response.
 *
 * Per the HTTP specification, this interface includes properties for
 * each of the following:
 *
 * - Protocol version
 * - Status code and reason phrase
 * - Headers
 * - Message body
 *
 * Responses are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Response extends Message implements ResponseInterface
{

    protected static $messages = [
        //1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        //2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        //3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        //4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        499 => 'Client Closed Request',
        //5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error',
    ];
    protected $status = 200;
    protected $reasonPhrase = '';

    /**
     *
     * @return \Dewep\Http\Request
     */
    public static function bootstrap(): Response
    {
        $headers = Headers::bootstrap();
        $body = Stream::bootstrap();

        return new static(200, $headers, $body);
    }

    /**
     *
     * @param \Dewep\Http\Headers $headers
     * @param \Dewep\Http\StreamInterface $body
     */
    public function __construct(int $status = 200, Headers $headers,
            Stream $body)
    {
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode(): int
    {
        return $this->status;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @param int $code The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the
     *     provided status code; if none is provided, implementations MAY
     *     use the defaults as suggested in the HTTP specification.
     * @return static
     * @throws \InvalidArgumentException For invalid status code arguments.
     */
    public function withStatus($code, $reasonPhrase = ''): Response
    {
        if (!isset(static::$messages[$code])) {
            throw new InvalidArgumentException('Transferred to non-standard status code');
        }

        $clone = clone $this;
        $clone->status = $code;

        if (empty($reasonPhrase)) {
            $reasonPhrase = static::$messages[$code];
        }
        $clone->reasonPhrase = $reasonPhrase;

        return $clone;
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase()
    {
        if (!is_null($this->reasonPhrase)) {
            return $this->reasonPhrase;
        }
        if (isset(static::$messages[$this->status])) {
            return static::$messages[$this->status];
        }
        return '';
    }

    /**
     *
     * @param type $body
     * @return \Dewep\Http\Response
     * @throws HttpException
     */
    public function setBody($body): Response
    {
        $response = Config::get('response');

        if (is_string($response)) {
            if ($response == Resp::TYPE_JSON) {
                $head = Resp::HTTP_JSON;
                $handler = '\Dewep\Parsers\Response::json';
            } elseif ($response == Resp::TYPE_XML) {
                $head = Resp::HTTP_XML;
                $handler = '\Dewep\Parsers\Response::xml';
            } elseif ($response == Resp::TYPE_HTML) {
                $head = Resp::HTTP_HTML;
                $handler = '\Dewep\Parsers\Response::html';
            } else {
                throw new HttpException('Specified is not a valid response type.');
            }
        } else {
            $head = $response['head'] ?? Resp::HTTP_JSON;
            $handler = $response['handler'] ?? '\Dewep\Parsers\Response::json';
        }

        $content = call_user_func($handler, $body);

        $stream = new Stream(fopen('php://temp', 'r+'));
        $stream->write($content);

        $clone = $this->withBody($stream);
        return $clone->withHeader(HeaderType::CONTENT_TYPE, $head);
    }

    /**
     *
     * @return string
     */
    public function __toString(): string
    {
        $http = sprintf(
                'HTTP/%s %s %s', $this->getProtocolVersion(),
                $this->getStatusCode(), $this->getReasonPhrase()
        );
        header($http, true);

        foreach ($this->getHeaders() as $name => $values) {
            $line = sprintf('%s: %s', $name, $this->getHeaderLine($name));
            header($line, true);
        }
        return (string) $this->getBody();
    }

}
