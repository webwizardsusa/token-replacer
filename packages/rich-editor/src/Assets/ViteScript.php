<?php

namespace Filapress\RichEditor\Assets;

use Illuminate\Support\Facades\Vite;

class ViteScript extends AbstractAsset
{


    public function toArray(): array
    {
        return [
            'type' => 'script',
            'module' => Vite::isRunningHot(),
            'src' => Vite::asset($this->getSrc())
        ];
    }
}
