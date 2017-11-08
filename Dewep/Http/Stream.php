<?php

namespace Dewep\Http;

use Psr\Http\Message\StreamInterface;
use Dewep\Exception\RuntimeException;

/**
 * Description of Stream
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Stream implements StreamInterface
{

    const PIPE = 4480;
    const OTHER = 33206;

    private static $modes = [
        'readable' => ['r', 'r+', 'w+', 'a+', 'x+', 'c+'],
        'writable' => ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+'],
    ];
    private $handle;
    private $pipe = false;

    public static function init($handle = null)
    {
        if (!is_resource($handle)) {
            $handle = fopen('php://temp', 'w+');
            stream_copy_to_stream(fopen('php://input', 'r'), $handle);
            rewind($handle);
        }
        return new static($handle);
    }

    public function __construct($handle = null)
    {
        if (!is_resource($handle)) {
            throw new RuntimeException('Not supplied resource.');
        }
        $this->handle = $handle;
        $stat = fstat($this->handle);
        if ($stat['mode'] == self::PIPE) {
            $this->pipe = true;
        }
    }

    public function __toString()
    {
        try {
            $this->rewind();
            return $this->getContents();
        } catch (\Exception $e) {
            return '';
        }
    }

    public function close()
    {
        if ($this->pipe) {
            pclose($this->handle);
        } else {
            fclose($this->handle);
        }
    }

    public function detach()
    {
        $old = $this->handle;

        $this->handle = null;
        $this->pipe = false;

        return $old;
    }

    public function eof()
    {
        return feof($this->handle);
    }

    public function getContents()
    {
        if (
                !$this->isReadable() ||
                ($contents = stream_get_contents($this->handle)) === false
        ) {
            throw new RuntimeException('Could not get contents of stream.');
        }
        return $contents;
    }

    public function getMetadata($key = null)
    {
        $meta = stream_get_meta_data($this->handle);
        if (is_null($key) === true) {
            return $meta;
        }
        return $meta[$key] ?? null;
    }

    public function getSize()
    {
        $stats = fstat($this->handle);
        return $stats['size'] ?? null;
    }

    public function isReadable()
    {
        $mode = $this->getMetadata('mode');
        $readeble = array_filter(self::$modes['readable'],
                function($v) use ($mode) {
            return stripos($mode, $v) !== false;
        }, ARRAY_FILTER_USE_KEY);
        return !empty($readeble);
    }

    public function isSeekable()
    {
        $mode = $this->getMetadata('seekable');

        return $mode ?? false;
    }

    public function isWritable()
    {
        $mode = $this->getMetadata('mode');
        $writable = array_filter(self::$modes['writable'],
                function($v) use ($mode) {
            return stripos($mode, $v) !== false;
        }, ARRAY_FILTER_USE_KEY);
        return !empty($writable);
    }

    public function read($length)
    {
        if (
                !$this->isReadable() ||
                ($data = fread($this->handle, $length)) === false
        ) {
            throw new RuntimeException('Could not read from stream');
        }
        return $data;
    }

    public function rewind()
    {
        if (
                !$this->isSeekable() ||
                rewind($this->handle) === false
        ) {
            throw new RuntimeException('Could not rewind stream');
        }
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        if (
                !$this->isSeekable() ||
                fseek($this->handle, $offset, $whence) === -1
        ) {
            throw new RuntimeException('Could not seek in stream');
        }
    }

    public function tell()
    {
        if (($position = ftell($this->handle)) === false || $this->pipe) {
            throw new RuntimeException('Could not get the position of the pointer in stream');
        }
        return $position;
    }

    public function write($string)
    {
        if (
                !$this->isWritable() ||
                ($written = fwrite($this->handle, $string)) === false
        ) {
            throw new RuntimeException('Could not write to stream');
        }
        return $written;
    }

}
