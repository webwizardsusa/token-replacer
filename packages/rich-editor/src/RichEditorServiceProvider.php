<?php

namespace Filapress\RichEditor;

use Carbon\Laravel\ServiceProvider;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Facades\FilamentAsset;

class RichEditorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filapress-rich-editor');
    }

    public function boot(): void
    {

        FilamentAsset::register([
            AlpineComponent::make('editor', __DIR__.'/../dist/filapress-rich-editor.js'),
        ], 'filapress/rich-editor');
    }
}
