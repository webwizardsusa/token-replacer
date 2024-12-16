<?php

namespace Filapress\Media\Actions;

use Closure;
use Filapress\Media\Support\FileUtils;
use File;
use Intervention\Image\Image;
use Storage;
use Symfony\Component\Mime\MimeTypes;

class CreateVariationAction
{
    protected string $name;

    protected Closure $action;

    protected array $sizes = [];

    protected int $quality = 90;

    protected ?string $format = null;

    public function __construct(string $name, Closure $action)
    {
        $this->name = $name;
        $this->action = $action;
    }

    /**
     * Sets the format and quality for the current instance.
     *
     * @param string|null $format The desired format, or null to unset.
     * @param int $quality The quality value, defaults to 90.
     *
     * @return static
     */
    public function format(?string $format, int $quality = 90): static
    {
        $this->format = $format;
        $this->quality = $quality;

        return $this;
    }

    /**
     * Set the sizes for the current instance.
     *
     * @param array $sizes An array of sizes to be assigned.
     * @return static Returns the current instance with assigned sizes.
     */
    public function sizes(array $sizes): static
    {
        $this->sizes = $sizes;

        return $this;
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * Create a new instance of the class with the specified name and action.
     *
     * @param string $name The descriptive name for the instance.
     * @param Closure $action The action to be associated with the instance.
     * @return static Returns a new instance of the class.
     */
    public static function make(string $name, Closure $action): static
    {
        return app(static::class, compact('name', 'action'));
    }

    /**
     * Generates an image along with its responsive sizes, saving them to the specified disk and path.
     *
     * @param Image $source The source image to be processed.
     * @param string|null $disk The disk where the image and its sizes will be stored. Null if not specified.
     * @param string|null $path The path where the image and its sizes will be stored. Null to return raw images.
     *
     * @return array|null Returns an array containing details of the generated image and its responsive sizes,
     *                    or null if the generation fails. The array includes:
     *                    - The main image data with details such as disk, path, width, height, filesize, and mime type.
     *                    - An array of sizes with corresponding details for each size.
     */
    public function generate(Image $source, ?string $disk, ?string $path): ?array
    {

        $returnRaw = ! $path;
        $formatted = call_user_func($this->action, $source);
        if (! $formatted) {
            return null;
        }

        $results = [
            'sizes' => [],
        ];

        if ($returnRaw) {
            $results['image'] = $formatted;

        } else {
            $filename = $this->saveFile($formatted, $path, $disk);

            $results['image'] = [
                'disk' => $disk,
                'path' => $filename,
                'width' => $formatted->width(),
                'height' => $formatted->height(),
                'filesize' => $this->fileSize($filename, $disk),
                'mime' => (new MimeTypes)->getMimeTypes(pathinfo($filename, PATHINFO_EXTENSION))[0],
            ];
        }

        foreach ($this->sizes as $size) {
            $newWidth = $size * config('filapress.media.responsive_variance', 1.5);
            if ($formatted->width() < $newWidth) {
                $image = $this->generateSize($size, $formatted);
                if ($returnRaw) {
                    $results['sizes'][$size] = $image;
                } else {
                    $filename = $this->saveFile($image, $path, $disk, $size);
                    $results['sizes'][$size] = [
                        'path' => $filename,
                        'width' => $image->width(),
                        'height' => $image->height(),
                        'filesize' => $this->fileSize($filename, $disk),
                        'mime' => (new MimeTypes)->getMimeTypes(pathinfo($filename, PATHINFO_EXTENSION))[0],
                    ];
                }
            }
        }

        return $results;
    }


    /**
     * Generates a resized image based on the specified size.
     *
     * @param int $size The desired width for the resized image.
     * @param Image $image The original image to be resized.
     * @return Image Returns a new resized image instance.
     */
    protected function generateSize(int $size, Image $image): Image
    {
        $instance = clone $image;

        return $instance->resize(width: $size);
    }

    /**
     * Retrieves the size of a file in bytes from the specified path.
     *
     * @param string $path The path to the file.
     * @param string|null $disk The name of the disk to retrieve the file size from.
     *                          If null, the default file system will be used.
     * @return int Returns the size of the file in bytes.
     */
    protected function fileSize(string $path, ?string $disk): int
    {
        if ($disk) {
            return Storage::disk($disk)->size($path);
        } else {
            return File::size($path);
        }
    }

    /**
     * Saves an image file to the specified path with optional modifications.
     *
     * @param Image $image The image instance to be saved.
     * @param string $path The file path where the image will be stored.
     * @param string|null $disk The storage disk to use. If null, the local filesystem is used.
     * @param int|null $size An optional size to append to the file name. Default is null.
     * @return string Returns the path where the image was saved.
     */
    protected function saveFile(Image $image, string $path, ?string $disk, ?int $size = null): string
    {
        $append = '-'.$this->name();
        if ($size) {
            $append .= '-'.$size;
        }
        $path = FileUtils::appendFileName($path, $append);

        if ($this->format) {
            $path = FileUtils::replaceExtension($path, $this->format);
        }

        $encoded = $image->encodeByExtension(pathinfo($path, PATHINFO_EXTENSION), quality: $this->quality);
        if ($disk) {
            Storage::disk($disk)->put($path, $encoded);
        } else {
            File::put($path, $encoded);
        }

        return $path;
    }
}
