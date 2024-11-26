<?php

namespace Filapress\RichEditor\Assets;

class ViteCss extends AbstractAsset
{
    public function toArray(): array
    {
        return [
            'type' => 'css',
            'src' => $this->getSrc()
        ];
    }
}
