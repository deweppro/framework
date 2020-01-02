<?php

declare(strict_types=1);

namespace Dewep\Middleware\Auth;

use Dewep\Exception\LogicException;
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
final class Cookies extends BaseClass
{
    public const ALGO_DEFAULT = 'gost-crypto';

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
     * @return mixed|null
     */
    public static function getData(string $key)
    {
        return self::$payload[$key] ?? null;
    }

    /**
     * @param mixed $value
     */
    public static function setData(string $key, $value): void
    {
        self::$payload[$key] = $value;
        self::$changed       = true;
    }

    public static function getAll(): array
    {
        return self::$payload;
    }

    public static function flush(): void
    {
        self::$payload = [];
        self::$changed = true;
    }

    public static function generateSecretKey(int $len = 64): ?string
    {
        try {
            return bin2hex(random_bytes($len));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @throws \Dewep\Exception\LogicException
     */
    public function before(
        Request $request,
        Response $response,
        array $params
    ): void {
        $this->setParams($params);

        $secret = $this->getParam('secret');
        if (empty($secret)) {
            throw new LogicException(
                'Secret key for JWT authorization not found!'
            );
        }

        $cookie = $request->getCookie()->get(
            $this->getParam('name', 'x-user-token'),
            ''
        );

        if (empty($cookie)) {
            return;
        }

        $tokenData = explode('.', $cookie);
        if (3 != count($tokenData)) {
            return;
        }

        [$header, $payload, $sign] = $tokenData;

        $headerData = $this->base64JsonDecode($header);
        if (
            ($headerData['alg'] ?? '') !== $this->getParam(
                'alg',
                self::ALGO_DEFAULT
            ) ||
            (int) ($headerData['exp'] ?? 0) < time()
        ) {
            self::$payload = [];

            return;
        }

        $verify = base64_encode(
            hash_hmac(
                self::$header['alg'],
                $header.'.'.$payload,
                $secret,
                true
            )
        );

        if (false === hash_equals($sign, $verify)) {
            return;
        }

        self::$payload = self::base64JsonDecode($payload);
    }

    /**
     * @throws \Dewep\Exception\LogicException
     */
    public function after(
        Request $request,
        Response $response,
        array $params
    ): void {
        $this->setParams($params);

        if (self::$changed) {
            $response->getCookie()->set(
                $this->getParam('name', 'x-user-token'),
                $this->buildToken(),
                self::$header['exp'],
                '/',
                $this->getParam('domain')
            );
        }
    }

    /**
     * @throws \Dewep\Exception\LogicException
     */
    public function buildToken(): string
    {
        $secret = $this->getParam('secret');
        if (empty($secret)) {
            throw new LogicException(
                'Secret key for JWT authorization not found!'
            );
        }

        if (0 == (int) self::$header['exp']) {
            self::$header['exp'] = time() + (int) $this->getParam('exp', 3600);
        }

        $header  = $this->base64JsonEncode(self::$header);
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

    protected function base64JsonDecode(string $str): array
    {
        $data = json_decode((string) base64_decode($str), true);
        if (!is_array($data)) {
            return [];
        }

        return $data;
    }

    protected function base64JsonEncode(array $data): string
    {
        $str = base64_encode((string) json_encode($data));
        if (!is_string($str)) {
            return '';
        }

        return $str;
    }
}
