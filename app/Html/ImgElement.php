<?php

namespace App\Html;

use Webwizardsusa\HtmlRefiner\CustomElement;

class ImgElement extends CustomElement
{
    protected array $attributes = ['src', 'alt', 'image-id', 'caption'];

    protected bool $selfClosing = false;

    public function tag(): string
    {
        return 'img';
    }
}
