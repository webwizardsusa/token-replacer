<?php

namespace Filapress\RichEditor\Plugins;

use Filapress\RichEditor\Support\HelpView;

class Blockquote extends AbstractPlugin
{
    public static function make(): static
    {
        return new static;
    }

    public function name(): string
    {
        return 'blockquote';
    }

    public function getHelp(): mixed
    {
        return HelpView::make('Blockquote')
            ->text('Used to indicate a block of text that is a quotation from another source.');

    }
}
