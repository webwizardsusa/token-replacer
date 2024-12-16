<?php

namespace Filapress\Media\Actions;

use Filapress\Media\Images\ImageFactory;
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

    /**
     * Creates a new instance of the class using the provided Image instance and size.
     *
     * @param Image $image The image to be cloned for the new instance.
     * @param int $size The size associated with the new instance.
     * @return static A newly instantiated object of the class.
     */
    public static function fromImage(Image $image, int $size): static
    {
        return app(static::class, ['image' => clone $image, 'size' => $size]);
    }

    /**
     * Create a new instance of the class from a given file path and size.
     *
     * @param string $path The file path to the image.
     * @param int $size The size to assign to the image instance.
     *
     * @return static A new instance of the class.
     */
    public static function fromPath(string $path, int $size): static
    {
        $image = ImageFactory::make()->fromPath($path);

        return app(static::class, ['image' => $image, 'size' => $size]);
    }

    /**
     * Create a new instance of the class from a specified storage disk and file path with a defined size.
     *
     * @param string $disk The name of the storage disk.
     * @param string $path The file path on the storage disk.
     * @param int $size The size to assign to the image instance.
     *
     * @return static A new instance of the class.
     */
    public static function fromStorage(string $disk, string $path, int $size): static
    {
        $image = ImageFactory::make()->fromStorage($disk, $path);

        return app(static::class, ['image' => $image, 'size' => $size]);
    }

    /**
     * Save the generated image to a specified disk and path.
     *
     * @param string $disk The storage disk where the image will be saved.
     * @param string $path The path on the disk where the image will be stored, including the file name and extension.
     *
     * @return static The current instance of the class.
     */
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

    /**
     * Generate a scaled version of the image if it exceeds the defined size limit.
     *
     * This method checks if the image width exceeds the permissible size,
     * defined by the specified size and a configurable variance. If it does,
     * the image is scaled down to the desired size. If the image has already
     * been scaled, it returns the current instance without further processing.
     *
     * @return static The current instance, possibly with the image scaled.
     */
    public function generate(): static
    {
        if ($this->generated !== null) {
            return $this;
        }
        if ($this->image->width() > $this->size * config('filapress.media.responsive_variance', 1.5)) {
            $this->generated = $this->image->scale(width: $this->size);

        }

        return $this;
    }

    /**
     * Retrieve the results of the operation.
     *
     * @return ImageInfo|null The results of the operation, or null if no results are available.
     */
    public function getResults(): ?ImageInfo
    {
        return $this->results;
    }
}
