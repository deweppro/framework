<?php

/*
 * The MIT License
 *
 * Copyright 2017 Mikhail Knyazhev <markus621@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Dewep;

use Dewep\Patterns\Registry;
use Dewep\Parsers\Yaml;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Config extends Registry
{

    const ROOT_DIR = 'root';
    const DATA_DIR = 'data';
    const DB_DIR = 'db';
    const SRC_DIR = 'src';
    const TEMP_DIR = 'temp';
    const VIEW_DIR = 'view';
    const WWW_DIR = 'www';
    const STORAGE_DIR = 'storage';

    /**
     *
     * @param array $config
     */
    final public static function append(array $config)
    {
        self::$__registry[self::_class()] = $config;
    }

    /**
     *
     * @param string $file
     */
    final public static function fromYaml(string $file)
    {
        $config = Yaml::read($file, self::dirTemp());
        self::append($config);
    }

    /**
     *
     * @return string
     */
    final public static function dirRoot(): string
    {
        $root = self::get(self::ROOT_DIR);
        if (empty($root)) {
            $root = strtr($_SERVER['DOCUMENT_ROOT'], '\\', '/');
            $root = trim($root, '/');
            $rootArray = explode('/', $root);
            $rootArray = array_slice($rootArray, 0, -1);
            $root = implode('/', $rootArray);
            self::set(self::ROOT_DIR, "/{$root}");
        }

        return self::get(self::ROOT_DIR);
    }

    /**
     *
     * @return string
     */
    final public static function dirData(): string
    {
        return self::dirRoot() . '/' . self::DATA_DIR;
    }

    /**
     *
     * @return string
     */
    final public static function dirDb(): string
    {
        return self::dirRoot() . '/' . self::DB_DIR;
    }

    /**
     *
     * @return string
     */
    final public static function dirSrc(): string
    {
        return self::dirRoot() . '/' . self::SRC_DIR;
    }

    /**
     *
     * @return string
     */
    final public static function dirTemp(): string
    {
        return self::dirRoot() . '/' . self::TEMP_DIR;
    }

    /**
     *
     * @return string
     */
    final public static function dirView(): string
    {
        return self::dirRoot() . '/' . self::VIEW_DIR;
    }

    /**
     *
     * @return string
     */
    final public static function dirWww(): string
    {
        return self::dirRoot() . '/' . self::WWW_DIR;
    }

    /**
     *
     * @return string
     */
    final public static function dirStorage(): string
    {
        return self::dirRoot() . '/' . self::STORAGE_DIR;
    }

    /**
     *
     */
    final public static function makeSysFolders()
    {
        $dirs = [
            self::dirData(),
            self::dirDb(),
            self::dirSrc(),
            self::dirStorage(),
            self::dirTemp(),
            self::dirView(),
            self::dirWww(),
        ];

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0777);
            }
        }
    }

}
