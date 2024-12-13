<?php

namespace Filapress\Media\Actions;

use Filapress\Media\Models\FilapressMedia;

class ImageSizeGenerator
{
    protected FilapressMedia $media;

    public function __construct(FilapressMedia $media)
    {
        $this->media = $media;
    }

    public static function make(FilapressMedia $media): static
    {
        return new static($media);
    }

    public function generate() {}
}
