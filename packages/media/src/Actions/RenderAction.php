<?php

namespace Filapress\Media\Actions;

use Filapress\Media\Models\FilapressMedia;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;

class RenderAction implements Renderable, Htmlable
{

    protected FilapressMedia $media;

    protected ?string $variant = null;

    protected array $attributes = [];

    protected bool $preview = false;

    protected bool $fallbackToOriginal = false;


    public function __construct(FilapressMedia $media) {

        $this->media = $media;
    }


    public static function make(FilapressMedia $media): static
    {
        return app(static::class, compact('media'));
    }

    public function attributes(array $attributes): static
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function variant(?string $variant): static
    {
        $this->variant = $variant;
        return $this;
    }

    public function preview(bool $preview = true): static
    {
        $this->preview = $preview;
        return $this;
    }

    public function fallbackToOriginal(bool $fallbackToOriginal = true): static
    {
        $this->fallbackToOriginal = $fallbackToOriginal;
        return $this;
    }

    public function render() {
        $media = $this->media;
        $variant = null;
        if ($this->variant) {
            $variant = $media->variants->where('name', $this->variant)->first();
        }

        return $this->media->getType()->render($media, $variant, $this->attributes, $this->preview);
    }


    public function toHtml()
    {
        return $this->render()->render();
    }
}
