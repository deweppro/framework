<?php declare(strict_types=1);

namespace Dewep\Middleware\Auth;

use Dewep\Http\Request;
use Dewep\Http\Response;
use Dewep\Middleware\BaseClass;

/**
 * @example :
 * Dewep\Middleware\CookieUserAuth:
 *          alg: gost-crypto
 *          exp: 3600
 *          secret: 1M8j4F0m8M6j4gay8Y3T
 *          name: x-user-token
 */
class Cookies extends BaseClass
{
    const ALGO_DEFAULT = 'gost-crypto';

    /** @var array */
    private static $payload = [];

    /** @var array */
    private static $header = [
        'alg' => self::ALGO_DEFAULT,
        'exp' => 0,
    ];

    /** @var bool */
    private static $changed = false;

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public static function getData(string $key)
    {
        return self::$payload[$key] ?? null;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public static function setData(string $key, $value)
    {
        self::$payload[$key] = $value;
        self::$changed = true;
    }

    /**
     * @return array
     */
    public static function getAll(): array
    {
        return self::$payload;
    }

    /**
     *
     */
    public static function flush()
    {
        self::$payload = [];
        self::$changed = true;
    }

    /**
     * @param int $len
     *
     * @return string
     * @throws \Exception
     */
    public static function generateSecretKey(int $len = 64)
    {
        return bin2hex(random_bytes($len));
    }

    /**
     * @param \Dewep\Http\Request  $request
     * @param \Dewep\Http\Response $response
     * @param array                $params
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public function before(Request $request, Response $response, array $params)
    {
        $this->setParams($params);

        $secret = $this->getParam('secret');
        if (empty($secret)) {
            throw new \Exception('Secret key for JWT authorization not found!');
        }

        $cookie = $request->getCookie()->get(
            $this->getParam('name', 'x-user-token'),
            ''
        );

        if (empty($cookie)) {
            return false;
        }

        $tokenData = explode('.', $cookie);
        if (count($tokenData) != 3) {
            return false;
        }

        [$header, $payload, $sign] = $tokenData;

        $headerData = $this->base64JsonDecode($header);
        if (
            ($headerData['alg'] ?? '') !== $this->getParam(
                'alg',
                self::ALGO_DEFAULT
            ) ||
            (int)($headerData['exp'] ?? 0) < time()
        ) {
            self::$payload = [];

            return false;
        }

        $verify = base64_encode(
            hash_hmac(
                self::$header['alg'],
                $header.'.'.$payload,
                $secret,
                true
            )
        );

        if (hash_equals($sign, $verify) === false) {
            return false;
        }

        self::$payload = self::base64JsonDecode($payload);

        return true;
    }

    /**
     * @param string $str
     *
     * @return array
     */
    protected function base64JsonDecode(string $str): array
    {
        $data = json_decode((string)base64_decode($str), true);
        if (!is_array($data)) {
            return [];
        }

        return $data;
    }

    /**
     * @param \Dewep\Http\Request  $request
     * @param \Dewep\Http\Response $response
     * @param array                $params
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public function after(Request $request, Response $response, array $params)
    {
        $this->setParams($params);

        if (!empty(self::$payload)) {

            $response->getCookie()->set(
                $this->getParam('name', 'x-user-token'),
                $this->buildToken(),
                self::$header['exp'],
                '/',
                $this->getParam('domain')
            );

            return true;

        }

        return false;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function buildToken(): string
    {
        $secret = $this->getParam('secret');
        if (empty($secret)) {
            throw new \Exception('Secret key for JWT authorization not found!');
        }

        if ((int)self::$header['exp'] == 0) {
            self::$header['exp'] = time() + (int)$this->getParam('exp', 3600);
        }

        $header = $this->base64JsonEncode(self::$header);
        $payload = $this->base64JsonEncode(self::$payload);

        $sign = base64_encode(
            hash_hmac(
                self::$header['alg'],
                $header.'.'.$payload,
                $secret,
                true
            )
        );

        return sprintf('%s.%s.%s', $header, $payload, $sign);
    }

    /**
     * @param array $data
     *
     * @return string
     */
    protected function base64JsonEncode(array $data): string
    {
        $str = base64_encode((string)json_encode($data));
        if (!is_string($str)) {
            return '';
        }

        return $str;
    }

}
