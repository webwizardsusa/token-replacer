<?php

namespace Filapress\Media\Elements;

use App\Models\OEmbed;
use Filapress\Media\Models\FilapressMedia;
use Webwizardsusa\HtmlRefiner\CustomElement;
use Webwizardsusa\HtmlRefiner\Element;
use Webwizardsusa\HtmlRefiner\RefinerDefinition;

class MediaElement extends CustomElement
{

    protected array $attributes = ['media', 'alt', 'link', 'align'];
    public function tag(): string
    {
        return 'fp-media';
    }

    public function render(string $html, RefinerDefinition $definition): string
    {
        $elements = $this->extract($html);
        $mediaIds = [];
        foreach ($elements as $element) {
            if ($element->hasAttribute('media')) {
                $mediaIds[$element->getAttribute('media')] = $element->getAttribute('media');
            }
        }

        $mediaItems = FilapressMedia::whereIn('id', $mediaIds)->get()->keyBy('id');
        foreach ($elements as $element) {
            $replace = '';
            if ($element->hasAttribute('media')) {

                /** @var FilapressMedia $media */
                $media = $mediaItems[$element->getAttribute('media')];
                if ($media) {
                    $caption = trim($element->getContent()) ? $element->getContent(): null;
                    if ($caption) {
                        $element->setAttribute('caption', $caption);
                    }
                    if ($definition->getContext() === 'editor') {
                        $rendered = $media->render(null, $element->getAttributes(), false)->render();
                        $rendered = str_replace('<a ', '<a onclick="return false;" ', $rendered);
                        $replace = $element->setAttribute('data-preview', $rendered)->render();

                    } else {
                        $attributes = $element->getAttributes();
                        $replace = $media->render(null, $attributes, false)->render();
                    }

                }
            }
            $html = str_replace($element->raw(), $replace, $html);
        }
        return $html;
    }
}
