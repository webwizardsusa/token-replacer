<?php

namespace Filapress\RichEditor;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;

class RichEditorPlugin implements Plugin
{

    public function getId(): string
    {
        return 'filapress-rich-editor';
    }

    public function register(Panel $panel): void
    {
        FilamentView::registerRenderHook(PanelsRenderHook::HEAD_END, function(){
            return view('filapress-rich-editor::head');
        });
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
