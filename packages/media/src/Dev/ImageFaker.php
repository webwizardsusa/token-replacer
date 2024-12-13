<?php

namespace Filapress\Media\Dev;

use Filapress\Media\Images\ImageFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Geometry\Factories\CircleFactory;
use Intervention\Image\Geometry\Factories\RectangleFactory;

class ImageFaker
{
    protected ?int $width;

    protected ?int $height;

    protected ?string $alt;

    protected ?string $filename;

    public static array $sizes = [
        [640, 480],
        [1920, 1080],
        [1600, 900],
        [1280, 720],
        [800, 600],
    ];

    public function __construct(?int $width = null, ?int $height = null, ?string $filename = null)
    {
        $this->width = $width;
        $this->height = $height;
        $this->filename = $filename ?? \Str::snake(fake()->words(rand(3, 5), true));
        if (! $this->width || ! $this->height) {
            $size = Arr::random(static::$sizes);
            if (rand(1, 5) > 3) {
                $this->width = $size[1];
                $this->height = $size[0];
            } else {
                $this->width = $size[0];
                $this->height = $size[1];
            }
        }
    }

    public static function make(?int $width = null, ?int $height = null, ?string $filename = null): static
    {
        return app(static::class, [$width, $height, $filename]);
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function width(?int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function height(?int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function alt(?string $alt): static
    {
        $this->alt = $alt;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function filename(?string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function generate(): string
    {
        $filename = $this->filename.'.jpg';

        $url = 'https://picsum.photos/'.$this->getWidth().'/'.$this->getHeight();
        $response = Http::get($url);
        if ($response->successful()) {
            // Get the file content
            $imageContent = $response->body();

            // Generate a temporary file path
            $tempFilePath = sys_get_temp_dir().'/'.$filename;

            // Save the image content to the temporary file
            file_put_contents($tempFilePath, $imageContent);

            return $tempFilePath;
        } else {
            throw new \Exception('Could not generate image');
        }
    }

    protected function randomColor(): string
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }
    public function generateLocal(): string
    {
        $filename = $this->filename.'.jpg';

        $tempFilePath = sys_get_temp_dir().'/'.$filename;

        $image = ImageFactory::make()->create($this->getWidth(), $this->getHeight());

        $image->fill($this->randomColor());

        for ($i = 0; $i < 50; $i++) {
            $randomX1 = mt_rand(0, $this->getWidth());
            $randomY1 = mt_rand(0, $this->getHeight());
            $randomX2 = mt_rand(0, $this->getWidth() / 2);
            $randomY2 = mt_rand(0, $this->getHeight() / 2);

            if (rand(1,6) > 3) {
                $image->drawRectangle($randomX1, $randomY1, function(RectangleFactory $rectangle) use ($randomX2, $randomY2){
                    $rectangle->size($randomX2, $randomY2);
                    $rectangle->background($this->randomColor());
                });
            } else {
                $image->drawCircle($randomX1, $randomY1, function(CircleFactory $circle) use ($randomX2, $randomY2){
                    $circle->radius(rand(20, $this->width / 2));
                    $circle->background($this->randomColor());
                });
            }

        }

        // Save the image content to the temporary file
        file_put_contents($tempFilePath, $image->toJpeg(quality: 90));

        return $tempFilePath;
    }
}
