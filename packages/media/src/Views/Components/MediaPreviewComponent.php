<?php

namespace Filapress\Media\Views\Components;

use Closure;
use Filapress\Media\Models\FilapressMedia;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MediaPreviewComponent extends Component
{
    public FilapressMedia $media;

    public bool $allowFullscreen;

    /**
     * Create a new component instance.
     */
    public function __construct(FilapressMedia $media, bool $allowFullscreen = true)
    {
        //
        $this->media = $media;
        $this->allowFullscreen = $allowFullscreen;
    }

    public function render(): View|string|Closure
    {

        return function (array $data) {
            return 'filapress-media::components.media-preview';
        };
    }
}
