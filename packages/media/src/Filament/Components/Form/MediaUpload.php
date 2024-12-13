<?php

namespace Filapress\Media\Filament\Components\Form;

use Filament\Forms\Components\FileUpload;

class MediaUpload extends FileUpload
{
    protected function setUp(): void
    {

        $this->storeFiles(false);

        parent::setUp();
    }
}
