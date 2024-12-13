<?php

namespace App\Media\Collections;

use Filapress\Media\MediaCollection;

class ContentCollection extends MediaCollection
{

    public function label(): string
    {
        return 'Content';
    }

    public function variants(): array
    {
        return ['card'];
    }
}

