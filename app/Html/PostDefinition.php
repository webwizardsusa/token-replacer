<?php

namespace App\Html;

use Webwizardsusa\HtmlRefiner\Filters\AutoParagraph;
use Webwizardsusa\HtmlRefiner\Filters\HtmlFilter;
use Webwizardsusa\HtmlRefiner\RefinerDefinition;

class PostDefinition extends RefinerDefinition
{


    public function setup(): void
    {
        $this->customElement(OEmbedElement::make());
    }

    public function filters(): array
    {
        return [
            AutoParagraph::make(),
            HtmlFilter::make()->allowElement('ol')
                ->allowElement('ul')
                ->allowElement('li')
                ->allowElement('p')
                ->allowElement('strong')
                ->allowElement('em')
                ->allowElement('a', ['href', 'target'])
                ->allowElement('del')
                ->allowElement('s')
                ->allowElement('u')
                ->allowElement('h3')
                ->allowElement('h4')
                ->allowElement('h5')
                ->allowElement('blockquote')
        ];
    }
}
