<?php

namespace Filapress\RichEditor\Plugins;

use Filapress\RichEditor\Support\HelpView;

class Italic extends AbstractPlugin
{
    public static function make(): static
    {
        return new static();
    }
    public function name(): string
    {
        return 'italic';
    }

    public function getHelp(): mixed
    {
        return HelpView::make('Italic')
            ->text('Stylizes text by slanting it to the right (italicizing). Often used to emphasize certain words or phrases');

    }
}
