<?php

namespace App\Media\ImageVariants;

use Filapress\Media\ImageVariant;
use Intervention\Image\Image;

class Card extends ImageVariant
{
    public function name(): string
    {
        return 'card';
    }

    protected function process(Image $image): static
    {
        $this->generated = $image->cover(1200, 800, $this->option('focal_point', 'center'));

        return $this;
    }
}
