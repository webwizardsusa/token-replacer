<?php

namespace Filapress\RichEditor\Plugins;

use Filapress\RichEditor\Support\HelpView;

class OrderedList extends AbstractPlugin
{
    public static function make(): static
    {
        return new static;
    }

    public function name(): string
    {
        return 'orderedList';
    }

    public function getHelp(): mixed
    {
        return HelpView::make('Unordered List')
            ->text('An ordered list is a collection of items where the sequence or priority of the items matters. Each item is automatically numbered, creating a clear and structured format that highlights order, steps, or ranking.');

    }
}
