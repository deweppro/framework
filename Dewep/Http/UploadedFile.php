<?php

namespace Dewep\Http;

use Dewep\Exception\FileExeption;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Description of UploadedFile
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class UploadedFile implements UploadedFileInterface
{

    public $file = null;
    protected $name;
    protected $type;
    protected $size;
    protected $error = UPLOAD_ERR_OK;
    protected $moved = false;
    public $stream = null;

    public static function init()
    {
        $files = [];

        foreach ($_FILES as $id => $file) {
            $files[$id] = new static($file['tmp_name'] ?? null,
                    $file['name'] ?? null, $file['type'] ?? null,
                    $file['size'] ?? null, $file['error'] ?? null);
        }

        return $files;
    }

    public function __construct($file, $name, $type, $size, $error)
    {
        $this->file = $file;
        $this->name = $name;
        $this->type = $type;
        $this->size = $size;
        $this->error = $error;
    }

    public function getClientFilename()
    {
        return $this->name;
    }

    public function getClientMediaType()
    {
        return $this->type;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getStream()
    {
        if ($this->moved) {
            throw new FileExeption(sprintf('Uploaded file %1s has already been moved',
                    $this->name));
        }
        if (is_null($this->stream)) {
            $this->stream = new Stream(fopen($this->file, 'r'));
        }
        return $this->stream;
    }

    public function moveTo($targetPath)
    {
        if ($this->moved) {
            throw new FileExeption('Uploaded file already moved');
        }
        if (!move_uploaded_file($this->file, $targetPath)) {
            throw new FileExeption(sprintf('Error moving uploaded file %1s to %2s',
                    $this->name, $targetPath));
        }
        $this->moved = true;
    }

}
