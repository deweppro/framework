<?php

namespace Dewep\Http\Traits;

/**
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
trait Http
{

    /**
     *
     * @param type $key
     * @return type
     */
    protected function normalizeKey($key)
    {
        $key = strtr(strtolower($key), '_', '-');
        if (stripos($key, 'http-') === 0) {
            $key = substr($key, 5);
        }
        return $key;
    }

    protected function originalKey($key)
    {
        if (stripos($key, 'HTTP_') === 0) {
            $key = substr($key, 5);
        }
        $key = str_replace('_', ' ', $key);
        $key = ucwords(strtolower($key));
        return str_replace(' ', '-', $key);
    }

}
