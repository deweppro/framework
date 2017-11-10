<?php

namespace Dewep\Http;

use Dewep\Interfaces\ActionIntrface;

/**
 * Данные получаемые из заголовков запроса
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Headers implements ActionIntrface
{

    use HttpTrait;

    protected $headers;
    protected $serverParams;
    protected $cookies;

    /**
     * Загрузчик класса
     *
     * @return Headers
     */
    public static function bootstrap(): Headers
    {
        return new static($_SERVER, $_COOKIE);
    }

    /**
     * Конструктов с возможностью использования своего массива заголовков
     *
     * @param array $server
     * @param array $cookie
     */
    public function __construct(array $server, array $cookie)
    {
        $headers = array_filter($server,
                function($k) {
            return substr($k, 0, 5) == 'HTTP_';
        }, ARRAY_FILTER_USE_KEY);

        $serverParams = array_diff_key($server, $headers);

        $this->serverParams = array_map([$this, 'originalKey'], $serverParams);
        $this->cookies = array_map([$this, 'originalKey'], $cookie);

        foreach ($headers as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Получение списка всех заголовков
     *
     * @return array
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
     * @param string $key
     * @param string $value
     */
    public function set(string $key, string $value)
    {
        $this->headers[$this->normalizeKey($key)] = [$value];
    }

    /**
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
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
    public function get(string $key, array $default = []): array
    {
        $key = $this->normalizeKey($key);
        return $this->headers[$key] ?? $default;
    }

    /**
     *
     * @param type $key
     * @param type $value
     */
    public function add(string $key, array $value = [])
    {
        $key = $this->normalizeKey($key);
        $valueExist = $this->get($key, []);

        $this->set($key, array_merge($valueExist, $value));
    }

    /**
     *
     * @param type $key
     */
    public function remove(string $key)
    {
        $key = $this->normalizeKey($key);
        unset($this->headers[$key]);
    }

    /*
     *
     */

    /**
     *
     * @return array
     */
    private function contentType(): array
    {
        $contentType = $this->headers['content-type'][0] ?? '';
        $contentTypeArray = explode(';', strtolower($contentType), 2);
        $contentTypeArray = array_map('trim', $contentTypeArray);
        return $contentTypeArray;
    }

    /**
     *
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType()[0] ?? '';
    }

    /**
     *
     * @return array
     */
    public function getContentTypeParams(): array
    {
        $params = $this->contentType()[1] ?? '';
        return explode('=', $params, 2);
    }

    /*
     *
     */

    /**
     *
     * @return array
     */
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    /**
     *
     * @param string $key
     * @param string $default
     * @return string
     */
    public function getServerParam(string $key, string $default = null): string
    {
        $key = $this->originalKey($key);
        return $this->serverParams[$key] ?? $default;
    }

    /*
     *
     */

    /**
     *
     * @return array
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    /**
     *
     * @param string $key
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     */
    public function setCookies(string $key, string $value, int $expire = 3600,
            string $path = '/', string $domain = '*', bool $secure = false,
            bool $httponly = false)
    {
        $key = $this->originalKey($key);
        $this->cookies[$key] = $value;
        setcookie($key, $value, $expire, $path, $domain, $secure, $httponly);
    }

    /**
     *
     * @param string $key
     */
    public function removeCookies(string $key)
    {
        $key = $this->originalKey($key);
        unset($this->cookies[$key]);
        setcookie($key, '', time() - 1);
    }

}
