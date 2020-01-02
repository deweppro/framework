<?php

declare(strict_types=1);

namespace Dewep\Parsers;

use Dewep\Exception\FileException;
use Symfony\Component\Yaml\Yaml as Y;

final class Yaml
{
    /**
     * @throws \Dewep\Exception\FileException
     */
    public static function read(string $path, string $tempDir = null): array
    {
        $tempDir = $tempDir ?? sys_get_temp_dir();

        if (!is_file($path) || !is_readable($path)) {
            throw new FileException($path.' is not found or cannot be read.');
        }
        $tempFileName = self::getTempFileName($path, $tempDir);

        if (file_exists($tempFileName)) {
            $yaml = include $tempFileName;
        } else {
            $yaml = Y::parse((string)file_get_contents($path));
            if (is_writeable($tempDir)) {
                file_put_contents(
                    $tempFileName,
                    '<?php return '.var_export($yaml, true).';'
                );
            }
        }

        if (empty($yaml)) {
            $yaml = [];
        }

        return $yaml;
    }

    private static function getTempFileName(
        string $path,
        string $tempDir
    ): string {
        return $tempDir.'/'.hash('md5', $path.':'.filectime($path)).'.yml.php';
    }
}
