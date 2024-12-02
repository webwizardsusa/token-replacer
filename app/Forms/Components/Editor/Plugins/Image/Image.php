<?php

namespace App\Forms\Components\Editor\Plugins\Image;

use Filament\Forms\Components\Actions\Action;
use Filapress\RichEditor\Assets\ViteScript;
use Filapress\RichEditor\Contracts\PluginAsExternalAssets;
use Filapress\RichEditor\Contracts\PluginHasDialog;
use Filapress\RichEditor\Plugins\AbstractPlugin;

class Image extends AbstractPlugin implements PluginHasDialog, PluginAsExternalAssets
{

    public static function make(): static
    {
        return new static();
    }
    public function name(): string
    {
        return 'image';
    }

    public function externalAssets(): array
    {
        return [
            ViteScript::make('resources/js/Editor/OEmbed/oembed.js'),
        ];
    }

    public function dialog(): Action
    {
        return Action::make('test');
    }
}
