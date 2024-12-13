<?php

namespace Filapress\RichEditor\Plugins;

use Filapress\RichEditor\Support\HelpView;

class UnorderedList extends AbstractPlugin
{
    public static function make(): static
    {
        return new static;
    }

    public function name(): string
    {
        return 'unorderedList';
    }

    public function getHelp(): mixed
    {
        return HelpView::make('Unordered List')
            ->text('An unordered list is a collection of items where the order of the items does not matter. Each item in the list is typically preceded by a bullet point or other marker, making it useful for grouping related pieces of information in a visually distinct way.');

    }
}
