<?php

namespace Filapress\Media\Images;

use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;

class ImageFactory
{
    protected string $driver;

    protected ImageManager $manager;

    public function __construct(string $driver = 'gd')
    {

        $this->driver = $driver;
        $this->manager = new ImageManager($driver === 'imagick' ? new ImagickDriver : new GdDriver);
    }

    public static function make(string $driver = 'gd'): ImageFactory
    {
        return app(static::class, compact('driver'));
    }

    public function fromPath(string $path): \Intervention\Image\Interfaces\ImageInterface
    {
        $image = $this->manager->read($path);

        return $image;
    }

    public function fromStorage(string $disk, string $path): \Intervention\Image\Interfaces\ImageInterface
    {
        $image = $this->manager->read(\Storage::disk($disk)->path($path));

        return $image;
    }

    public function create(int $width, int $height): ImageInterface {
        return $this->manager->create($width, $height);
    }
}
