<?php

namespace App\Providers;

use App\Html\CustomElements;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filapress\RichEditor\FPRichEditor;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
       $this->app->singleton(CustomElements::class, fn () => new CustomElements(config('custom-html-elements.elements', [])));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        if (Vite::isRunningHot()) {
            FPRichEditor::$scriptSrc = Js::make('editor', Vite::asset('packages/rich-editor/resources/js/FPRichEditor.js'))->module();
        }

    }
}
