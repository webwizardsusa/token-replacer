<?php

namespace Filapress\Media\Editor;

use Filapress\Core\Assets\FilapressJs;
use Filapress\Media\Elements\MediaElement;
use Filapress\RichEditor\Contracts\PluginAsExternalAssets;
use Filapress\RichEditor\Plugins\AbstractPlugin;

class MediaPlugin extends AbstractPlugin implements PluginAsExternalAssets
{
    public ?string $collection = null;

    public function name(): string
    {
        return 'media';
    }

    public static function make(): static
    {
        return new static;
    }

    public function collection(?string $collection): static
    {
        $this->collection = $collection;

        return $this;
    }

    public function getCollection(): ?string
    {
        return $this->collection;
    }

    public function externalAssets(): array
    {
        return [
            FilapressJs::getInstance('media-editor-plugin', 'filapress/media'),
        ];
    }

    public function getConfig(): array
    {
        return ['collection' => $this->collection];
    }

    public function stateDehydrate($state): mixed
    {
        // Sometimes TipTap doesn't fully transform the final html, so here we will clean it up.
        $items = MediaElement::make()
            ->extract($state);

        foreach ($items as $item) {
            $attributes = \Arr::only($item->getAttributes(), ['media', 'alt', 'align', 'link']);
            $item->setAttributes($attributes);
            $state = str_replace($item->raw(), $item->render(), $state);
        }

        return $state;
    }
}
