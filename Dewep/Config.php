<?php

namespace Dewep;

use Dewep\Parsers\Yaml;
use Dewep\Patterns\Registry;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Config extends Registry
{

    const ROOT_DIR    = 'root';
    const DATA_DIR    = 'data';
    const DB_DIR      = 'db';
    const SRC_DIR     = 'src';
    const TEMP_DIR    = 'temp';
    const VIEW_DIR    = 'view';
    const WWW_DIR     = 'www';
    const STORAGE_DIR = 'storage';

    /**
     * @param string $file
     * @throws Exception\FileException
     */
    final public static function fromYaml(string $file)
    {
        $config = Yaml::read($file, self::dirTemp());
        self::append($config);
    }

    /**
     * @return string
     */
    final public static function dirTemp(): string
    {
        return self::dirRoot().'/'.self::TEMP_DIR;
    }

    /**
     * @return string
     */
    final public static function dirRoot(): string
    {
        $root = self::get(self::ROOT_DIR);
        if (empty($root)) {
            $root      = strtr($_SERVER['DOCUMENT_ROOT'], '\\', '/');
            $root      = trim($root, '/');
            $rootArray = explode('/', $root);
            $rootArray = array_slice($rootArray, 0, -1);
            $root      = implode('/', $rootArray);
            self::set(self::ROOT_DIR, "/{$root}");
        }

        return self::get(self::ROOT_DIR);
    }

    /**
     * @param array $config
     */
    final public static function append(array $config)
    {
        self::$__registry[self::__class()] = $config;
    }

    /**
     * @return array
     */
    final public static function makeSysFolders(): array
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

        return $dir;
    }

    /**
     * @return string
     */
    final public static function dirData(): string
    {
        return self::dirRoot().'/'.self::DATA_DIR;
    }

    /**
     * @return string
     */
    final public static function dirDb(): string
    {
        return self::dirRoot().'/'.self::DB_DIR;
    }

    /**
     * @return string
     */
    final public static function dirSrc(): string
    {
        return self::dirRoot().'/'.self::SRC_DIR;
    }

    /**
     * @return string
     */
    final public static function dirStorage(): string
    {
        return self::dirRoot().'/'.self::STORAGE_DIR;
    }

    /**
     * @return string
     */
    final public static function dirView(): string
    {
        return self::dirRoot().'/'.self::VIEW_DIR;
    }

    /**
     * @return string
     */
    final public static function dirWww(): string
    {
        return self::dirRoot().'/'.self::WWW_DIR;
    }

}
