<?php

namespace Filapress\Media\Contracts;

use Filapress\Media\Models\FilapressMedia;

interface GeneratesFakeMedia
{
    public function fake(FilapressMedia $media): FilapressMedia;
}
