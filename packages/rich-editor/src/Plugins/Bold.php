<?php

namespace Filapress\RichEditor\Plugins;

use Filapress\RichEditor\Support\HelpView;

class Bold extends AbstractPlugin
{
    public static function make(): static
    {
        return new static();
    }
    public function name(): string
    {
        return 'bold';
    }

    public function getHelp(): mixed
    {
        return HelpView::make('Bold')
            ->text('Emphasis a part of your text by making it appear bolder');

    }
}
