<?php

namespace Filapress\Media\Filament\Components\Form;

use Closure;
use Filament\Forms\Components\Field;
use Filapress\Media\Models\FilapressMedia;

class MediaBrowserField extends Field
{
    protected string $view = 'filapress-media::components.form.media-browser-field';

    protected array|Closure $types = [];
    protected string|Closure|null $collection = null;


    private ?FilapressMedia $media = null;

    public function setup(): void
    {
        $this->afterStateUpdated(function ($state) {});
        $this->live();
    }


    public function collection(string|Closure|null $collection): static
    {
        $this->collection = $collection;
        return $this;
    }

    public function getCollection(): ?string
    {
        return $this->evaluate($this->collection);
    }
    public function types(array|Closure $types): static
    {
        $this->types = $types;

        return $this;
    }

    public function getTypes(): array
    {
        return $this->evaluate($this->types);
    }

    public function getMedia(): ?FilapressMedia
    {
        if (! $this->media && $this->getState()) {
            $this->media = FilapressMedia::find($this->getState());
        }

        return $this->media;
    }

    public function mediaPreview()
    {
        return $this->getMedia()->render();
    }
}
