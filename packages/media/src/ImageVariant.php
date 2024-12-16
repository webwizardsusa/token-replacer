<?php

namespace Filapress\Media;

use Filapress\Media\Actions\ResponsiveSizeGenerator;
use Filapress\Media\Images\ImageFactory;
use Filapress\Media\Support\FileUtils;
use Filapress\Media\Support\ImageInfo;
use Illuminate\Support\Arr;
use Intervention\Image\Image;

abstract class ImageVariant
{
    protected ?Image $generated = null;

    protected ?string $format = null;

    protected int $quality;

    protected int $filesize = 0;

    protected ?string $mimeType = null;

    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Foundation\Application|mixed|null
     */
    protected array|bool $responsiveSizes;

    private array $options = [];

    protected array $results = [];

    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->quality = config('filapress.media.quality', 90);
        $this->responsiveSizes = true;
    }

    public function option(string $key, $default = null): mixed
    {
        return Arr::get($this->options, $key, $default);
    }

    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function responsive(bool|array $responsiveSizes): static
    {
        $this->responsiveSizes = $responsiveSizes;

        return $this;
    }

    public function getResponsiveSizes(): array
    {
        if (is_array($this->responsiveSizes)) {
            return $this->responsiveSizes;
        }

        if ($this->responsiveSizes) {
            return config('filapress.media.responsive_sizes', []);
        }

        return [];
    }

    abstract public function name(): string;

    abstract protected function process(Image $image): static;

    protected function guessFormat(Image $image): ?string
    {
        $mime = $image->origin()->mimetype();
        if ($mime) {
            return FileUtils::extensionFromMime($mime);
        }

        return null;
    }

    public function generateFromStorage(string $disk, string $path): static
    {
        $newImage = ImageFactory::make()->fromStorage($disk, $path);
        $this->format = $this->guessFormat($newImage);
        $this->process($newImage);

        return $this;
    }

    public function generateFromPath(string $path): static
    {
        $newImage = ImageFactory::make()->fromPath($path);
        $this->format = $this->guessFormat($newImage);
        $this->process($newImage);

        return $this;
    }

    public function generateFromImage(Image $image): static
    {
        $newImage = clone $image;
        $this->format = $this->guessFormat($newImage);
        $this->process($newImage);

        return $this;
    }

    public function isGenerated(): bool
    {
        return $this->generated !== null;
    }

    public function saveTo(string $path, ?string $disk = null): static
    {
        $this->doSave($this->generated, $path, $disk);
        $results = [
            'source' => new ImageInfo($path, $this->generated->width(), $this->generated->height(), \Storage::disk($disk)->size($path), $this->generated->origin()->mimetype()),
            'sizes' => [],
        ];

        foreach ($this->getResponsiveSizes() as $size) {
            $newPath = FileUtils::appendFileName($path, '-'.$size);
            $responsive = ResponsiveSizeGenerator::fromImage($this->generated, $size)
                ->saveTo($disk, $newPath)
                ->getResults();
            if ($responsive) {
                $results['sizes'][$size] = $responsive;
            }
        }
        $this->results = $results;

        return $this;

    }

    public function getResults(): array
    {
        return $this->results;
    }

    protected function doSave(Image $image, string $path, ?string $disk = null): static
    {
        if ($disk) {
            \Storage::disk($disk)->put($path, $image->encodeByExtension($this->format, quality: $this->quality));
        } else {
            file_put_contents($path, $image->encodeByExtension($this->format, quality: $this->quality));
        }

        return $this;
    }

    public function getWidth(): int
    {
        return (int) $this->generated?->width();
    }

    public function getHeight(): int
    {
        return (int) $this->generated?->width();
    }

    public function getSize(): int
    {
        return $this->filesize;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }
}
