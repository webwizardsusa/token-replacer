<?php

namespace Filapress\Media\Actions;

use Filapress\Media\Images\ImageFactory;
use Filapress\Media\Models\FilapressMedia;
use Filapress\Media\Support\FileUtils;
use Filapress\Media\Support\ImageInfo;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;

class ResponsiveSizeGenerator
{

    protected Image $image;
    protected int $size;
    protected ?Image $generated = null;

    protected ?ImageInfo $results = null;

    public function __construct(Image $image, int $size)
    {
        $this->image = $image;
        $this->size = $size;
    }

    public static function fromImage(Image $image, int $size): static
    {
        return app(static::class, ['image' => clone $image, 'size' => $size]);
    }

    public static function fromPath(string $path, int $size): static
    {
        $image = ImageFactory::make()->fromPath($path);
        return app(static::class, ['image' => $image, 'size' => $size]);
    }

    public static function fromStorage(string $disk, string $path, int $size): static
    {
        $image = ImageFactory::make()->fromStorage($disk, $path);
        return app(static::class, ['image' => $image, 'size' => $size]);
    }

    public function saveTo(string $disk, string $path): static
    {
        $this->generate();
        if ($this->generated) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            Storage::disk($disk)->put($path, $this->generated->encodeByExtension($type, quality: config('filapress.media.responsive_quality', 80)));
            $this->results = new ImageInfo($path, $this->generated->width(), $this->generated->height(), Storage::disk($disk)->size($path), $this->generated->origin()->mimetype());
        }
        return $this;
    }

    public function generate():static {
        if ($this->generated !== null) {
            return $this;
        }
        if ($this->image->width()  > $this->size * config('filapress.media.responsive_variance', 1.5)) {
            $this->generated = $this->image->scale(width: $this->size);

        }
        return $this;
    }

    public function getResults(): ?ImageInfo
    {
        return $this->results;
    }
}
