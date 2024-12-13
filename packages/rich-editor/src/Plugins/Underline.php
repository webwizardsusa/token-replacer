<?php

namespace Filapress\RichEditor\Plugins;

use Filapress\RichEditor\Support\HelpView;

class Underline extends AbstractPlugin
{
    public static function make(): static
    {
        return new static;
    }

    public function name(): string
    {
        return 'underline';
    }

    public function getHelp(): mixed
    {
        return HelpView::make('Underline')
            ->text('Underlines the text.');

    }
}
