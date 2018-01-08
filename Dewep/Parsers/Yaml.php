<?php

namespace Dewep\Parsers;

use Dewep\Exception\FileException;
use Symfony\Component\Yaml\Yaml as Y;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Yaml
{

    public static function read(string $path, string $tempDir = null)
    {
        $tempDir = $tempDir ?? sys_get_temp_dir();
        $yaml = [];
        if (is_file($path) && is_readable($path)) {
            $tempFileName = $tempDir . '/' . hash('md5',
                    $path . ':' . filectime($path)) . '.yml.json';

            if (file_exists($tempFileName)) {
                $yaml = json_decode(file_get_contents($tempFileName), true);
            } else {
                $yaml = Y::parse(file_get_contents($path));
                if (is_writeable($tempDir)) {
                    file_put_contents($tempFileName,
                        json_encode($yaml,
                            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                }
            }

            if (empty($yaml)) {
                $yaml = [];
            }
        } else {
            throw new FileException($path . ' is not found or cannot be read.');
        }
        return $yaml;
    }

}
