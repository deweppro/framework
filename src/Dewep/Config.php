<?php declare(strict_types=1);

namespace Dewep;

use Dewep\Exception\RuntimeException;
use Dewep\Parsers\Yaml;
use Dewep\Patterns\Registry;

class Config extends Registry
{

    const BASE_PATH_NAME = 'base';
    const APP_PATH_NAME = 'app';
    const RESOURCES_PATH_NAME = 'resources';
    const DATABASE_PATH_NAME = 'database';
    const TEMP_PATH_NAME = 'temp';
    const STORAGE_PATH_NAME = 'storage';
    const TESTS_PATH_NAME = 'tests';
    const PUBLIC_PATH_NAME = 'public';

    /**
     * @param string $file
     *
     * @throws Exception\FileException
     * @throws RuntimeException
     */
    final public static function setConfigPath(string $file)
    {
        if (
            !file_exists($file) ||
            !is_readable($file)
        ) {
            throw new RuntimeException('Config file not found!');
        }

        self::set(self::BASE_PATH_NAME, (string)realpath(dirname($file)));

        self::append(Yaml::read($file, self::tempPath()));
    }

    /**
     * @param array $config
     */
    final public static function append(array $config)
    {
        self::$__registry[self::__class()] = array_replace_recursive(
            self::$__registry[self::__class()] ?? [],
            $config
        );
    }

    /**
     * @return string
     */
    final public static function tempPath(): string
    {
        return (string)(self::basePath().'/'.self::TEMP_PATH_NAME);
    }

    /**
     * @return string
     */
    final public static function basePath(): string
    {
        return self::get(self::BASE_PATH_NAME);
    }

    final public static function restoreFolderStructure()
    {
        foreach (self::getPaths() as $name => $path) {
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
        }
    }

    /**
     * @return array
     */
    final public static function getPaths(): array
    {
        return [
            self::BASE_PATH_NAME      => self::basePath(),
            self::APP_PATH_NAME       => self::appPath(),
            self::RESOURCES_PATH_NAME => self::resourcesPath(),
            self::DATABASE_PATH_NAME  => self::databasePath(),
            self::TEMP_PATH_NAME      => self::tempPath(),
            self::STORAGE_PATH_NAME   => self::storagePath(),
            self::TESTS_PATH_NAME     => self::testsPath(),
            self::PUBLIC_PATH_NAME    => self::publicPath(),
        ];
    }

    /**
     * @return string
     */
    final public static function appPath(): string
    {
        return (string)(self::basePath().'/'.self::APP_PATH_NAME);
    }

    /**
     * @return string
     */
    final public static function resourcesPath(): string
    {
        return (string)(self::basePath().'/'.self::RESOURCES_PATH_NAME);
    }

    /**
     * @return string
     */
    final public static function databasePath(): string
    {
        return (string)(self::basePath().'/'.self::DATABASE_PATH_NAME);
    }

    /**
     * @return string
     */
    final public static function storagePath(): string
    {
        return (string)(self::basePath().'/'.self::STORAGE_PATH_NAME);
    }

    /**
     * @return string
     */
    final public static function testsPath(): string
    {
        return (string)(self::basePath().'/'.self::TESTS_PATH_NAME);
    }

    /**
     * @return string
     */
    final public static function publicPath(): string
    {
        return (string)(self::basePath().'/'.self::PUBLIC_PATH_NAME);
    }

}
