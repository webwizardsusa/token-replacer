<?php

namespace Filapress\RichEditor\Plugins;

use Filapress\RichEditor\Support\HelpView;

class Strike extends AbstractPlugin
{
    public static function make(): static
    {
        return new static();
    }
    public function name(): string
    {
        return 'strike';
    }

    public function getHelp(): mixed
    {
        return HelpView::make('Strikethrough')
            ->text('Adds a line through the text, indicating that it is no longer relevant or has been deleted.');

    }
}
