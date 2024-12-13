<?php

namespace Filapress\Media\Support;

use Illuminate\Contracts\Support\Arrayable;

class ImageInfo implements Arrayable
{

    protected string $path;
    protected int $width;
    protected int $height;
    protected int $filesize;
    protected string $mime;

    public function __construct(string $path, int $width, int $height, int $filesize, string $mime)
    {
        $this->path = $path;
        $this->width = $width;
        $this->height = $height;
        $this->filesize = $filesize;
        $this->mime = $mime;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getFilesize(): int
    {
        return $this->filesize;
    }

    public function getMime(): string
    {
        return $this->mime;
    }


    public function toArray(): array
    {
        return [
            'path' => $this->path,
            'width' => $this->width,
            'height' => $this->height,
            'filesize' => $this->filesize,
            'mime' => $this->mime,
        ];
    }
}
