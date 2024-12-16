<?php

namespace Filapress\Media\Actions;

use Filapress\Media\Images\ImageFactory;
use Filapress\Media\Models\FilapressMedia;
use Filapress\Media\Support\FileUtils;
use Illuminate\Support\Facades\Storage;
use Webwizardsusa\TokenReplace\TokenReplacer;
use Webwizardsusa\TokenReplace\Transformers\DateTransformer;
use Webwizardsusa\TokenReplace\Transformers\FileTransformer;

class ThumbnailGenerator
{
    protected FilapressMedia $media;

    public function __construct(FilapressMedia $media)
    {

        $this->media = $media;
    }

    public static function make(FilapressMedia $media): static
    {
        return app(static::class, ['media' => $media]);
    }

    public function generate(?int $width = null, ?int $height = null, ?int $quality = null): FilapressMedia
    {
        $width = $width ?? config('filapress.media.thumbnail_width');
        $height = $height ?? config('filapress.media.thumbnail_height');
        $quality = $quality ?? config('filapress.media.thumbnail_quality');
        if ($this->media->thumbnail_path && $this->media->thumbnail_disk) {
            if (Storage::disk($this->media->thumbnail_disk)->exists($this->media->thumbnail_path)) {
                Storage::disk($this->media->thumbnail_disk)->delete($this->media->thumbnail_path);
            }
        }

        $this->media->thumbnail_disk = config('filapress.media.thumbnail_disk');
        $path = (new TokenReplacer(config('filapress.media.thumbnail_path')))
            ->with('file', new FileTransformer($this->media->path))
            ->with('date', new DateTransformer)
            ->transform();
        $path = FileUtils::replaceExtension($path, 'jpg');
        $this->media->thumbnail_path = $path;
        $image = ImageFactory::make()->fromStorage($this->media->disk, $this->media->path);
        $image->cover($width, $height);

        Storage::disk($this->media->disk)
            ->put($this->media->thumbnail_path, $image->toJpeg($quality));

        return $this->media;
    }
}
