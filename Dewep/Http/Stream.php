<?php

namespace Dewep\Http;

use Psr\Http\Message\StreamInterface;
use Dewep\Exception\RuntimeException;

/**
 * Describes a data stream.
 *
 * Typically, an instance will wrap a PHP stream; this interface provides
 * a wrapper around the most common operations, including serialization of
 * the entire stream to a string.
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

    /**
     *
     * @return StreamInterface
     */
    public static function bootstrap($handle = null): Stream
    {
        if (!is_resource($handle)) {
            $handle = fopen('php://temp', 'r+');
            stream_copy_to_stream(fopen('php://input', 'r'), $handle);
            rewind($handle);
        }
        return new static($handle);
    }

    /**
     *
     * @param type $handle
     * @throws RuntimeException
     */
    public function __construct($handle = null)
    {
        if (!is_resource($handle)) {
            throw new RuntimeException('Not supplied resource.');
        }
        $this->handle = $handle;
        $stat = fstat($this->handle);
        if ($stat['mode'] == self::PIPE) {
            $this->pipe = true;
        } elseif ($stat['mode'] == self::OTHER) {
            $this->pipe = false;
        } else {
            throw new RuntimeException('Undefined resource mode.');
        }
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString()
    {
        try {
            $this->rewind();
            return $this->getContents();
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        if ($this->pipe) {
            pclose($this->handle);
        } else {
            fclose($this->handle);
        }
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $old = $this->handle;

        $this->handle = null;
        $this->pipe = false;

        return $old;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize(): int
    {
        $stats = fstat($this->handle);
        return $stats['size'] ?? null;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell(): int
    {
        if (($position = ftell($this->handle)) === false || $this->pipe) {
            throw new RuntimeException('Could not get the '
            . 'position of the pointer in stream');
        }
        return $position;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof(): bool
    {
        return feof($this->handle);
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        $mode = $this->getMetadata('seekable');

        return $mode ?? false;
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws \RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (
                !$this->isSeekable() ||
                fseek($this->handle, (int) $offset, (int) $whence) === -1
        ) {
            throw new RuntimeException('Could not seek in stream');
        }
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind()
    {
        if (
                !$this->isSeekable() ||
                rewind($this->handle) === false
        ) {
            throw new RuntimeException('Could not rewind stream');
        }
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable(): bool
    {
        $mode = $this->getMetadata('mode');
        $writable = array_filter(self::$modes['writable'],
                function($v) use ($mode) {
            return stripos($mode, $v) !== false;
        }, ARRAY_FILTER_USE_BOTH);
        return !empty($writable);
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string): int
    {
        if (
                !$this->isWritable() ||
                ($written = fwrite($this->handle, (string) $string)) === false
        ) {
            throw new RuntimeException('Could not write to stream');
        }
        return $written;
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable(): bool
    {
        $mode = $this->getMetadata('mode');
        $readeble = array_filter(self::$modes['readable'],
                function($v) use ($mode) {
            return stripos($mode, $v) !== false;
        }, ARRAY_FILTER_USE_BOTH);
        return !empty($readeble);
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length): string
    {
        if (
                !$this->isReadable() ||
                ($data = fread($this->handle, (int) $length)) === false
        ) {
            throw new RuntimeException('Could not read from stream');
        }
        return $data;
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents(): string
    {
        $this->rewind();
        if (
                !$this->isReadable() ||
                ($contents = stream_get_contents($this->handle)) === false
        ) {
            throw new RuntimeException('Could not get contents of stream.');
        }
        return $contents;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        $meta = stream_get_meta_data($this->handle);
        if (is_null($key) === true) {
            return $meta;
        }
        return $meta[(string) $key] ?? null;
    }

}
