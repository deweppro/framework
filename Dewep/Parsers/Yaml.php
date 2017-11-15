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

namespace Dewep\Parsers;

use Dewep\Exception\FileException;
use Symfony\Component\Yaml\Yaml as Y;

/**
 * Description of Yaml
 *
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
