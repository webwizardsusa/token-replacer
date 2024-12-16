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

    /**
     * Creates an instance of the class with the provided FilapressMedia object.
     *
     * This method uses the Laravel application service container to instantiate the class,
     * injecting the specified media object into the instance.
     *
     * @param FilapressMedia $media The media object to associate with the new instance.
     *
     * @return static A new instance of the class with the specified media object.
     */
    public static function make(FilapressMedia $media): static
    {
        return app(static::class, ['media' => $media]);
    }

    /**
     * Generates a thumbnail for the associated media object with the specified dimensions and quality.
     *
     * This method creates or updates the thumbnail for the media object by:
     * - Retrieving the width, height, and quality from the provided parameters or default configuration values.
     * - Removing the existing thumbnail if it exists.
     * - Generating a new thumbnail file path using a token replacer utility.
     * - Using an image factory to create and resize the image.
     * - Saving the processed thumbnail to storage.
     *
     * @param int|null $width The width of the thumbnail. Defaults to configuration value if not provided.
     * @param int|null $height The height of the thumbnail. Defaults to configuration value if not provided.
     * @param int|null $quality The quality of the thumbnail image. Defaults to configuration value if not provided.
     *
     * @return FilapressMedia The updated media object with the new thumbnail details.
     */
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
