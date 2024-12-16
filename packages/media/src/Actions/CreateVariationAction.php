<?php

namespace Filapress\Media\Actions;

use Closure;
use Filapress\Media\Support\FileUtils;
use Intervention\Image\Image;
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

    public function format(?string $format, int $quality = 90): static
    {
        $this->format = $format;
        $this->quality = $quality;

        return $this;
    }

    public function sizes(array $sizes): static
    {
        $this->sizes = $sizes;

        return $this;
    }

    public function name(): string
    {
        return $this->name;
    }

    public static function make(string $name, Closure $action): static
    {
        return app(static::class, compact('name', 'action'));
    }

    public function generate(Image $source, ?string $disk, ?string $path): ?array
    {

        $returnRaw = ! $path;
        $formatted = call_user_func($this->action, $source);
        if (! $formatted) {
            return null;
        }

        $results = [
            'image' => null,
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

    protected function fileData(Image $image, string $filename, ?string $disk): array {}

    protected function generateSize(int $size, Image $image): Image
    {
        $instance = clone $image;

        return $instance->resize(width: $size);
    }

    protected function fileSize(string $path, ?string $disk): int
    {
        if ($disk) {
            return \Storage::disk($disk)->size($path);
        } else {
            return \File::size($path);
        }
    }

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
            \Storage::disk($disk)->put($path, $encoded);
        } else {
            \File::put($path, $encoded);
        }

        return $path;
    }
}
