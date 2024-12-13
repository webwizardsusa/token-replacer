<?php

namespace App\Html;

use App\Models\OEmbed;
use Webwizardsusa\HtmlRefiner\CustomElement;
use Webwizardsusa\HtmlRefiner\Element;
use Webwizardsusa\HtmlRefiner\RefinerDefinition;

class OEmbedElement extends CustomElement
{
    protected array $attributes = ['src'];

    protected bool $selfClosing = false;

    public function tag(): string
    {
        return 'oembed';
    }

    public function render(string $html, RefinerDefinition $definition): string
    {
        $elements = $this->extract($html);

        foreach ($elements as $element) {
            $replace = '';
            if ($element->hasAttribute('src')) {
                $oembed = OEmbed::fromUrl($element->getAttribute('src'));
                if ($oembed) {
                    if ($definition->getContext() === 'editor') {
                        $replace = $element->setAttributes([
                            'src' => $element->getAttribute('src'),
                            'title' => $oembed->response->getTitle(),
                            'provider' => $oembed->response->getProvider(),
                        ])->render();
                    } else {
                        $replace = $oembed->response->render();
                    }

                } elseif ($definition->getContext() === 'editor') {

                    $replace = new Element('oembed', [
                        'src' => $element->getAttribute('src'),
                        'title' => 'OEmbed',
                        'provider' => 'OEmbed',
                        'invalid' => true,
                    ], '', true);
                }
            }
            $html = str_replace($element->raw(), $replace, $html);
        }

        return $html;
    }
}
