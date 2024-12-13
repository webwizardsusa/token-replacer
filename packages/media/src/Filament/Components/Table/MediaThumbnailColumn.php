<?php

namespace Filapress\Media\Filament\Components\Table;

use Filament\Tables\Columns\ImageColumn;
use Filapress\Media\Models\FilapressMedia;

class MediaThumbnailColumn extends ImageColumn
{
    protected function setUp(): void
    {
        $this->defaultImageUrl(function (FIlapressMedia $media) {
            return $media->thumbnail();
        });
        $this->width('200px');
        $this->height('200px');
        $this->extraAttributes(['class' => 'filapress-media-thumbnail']);
    }
}
