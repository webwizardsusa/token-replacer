<?php

namespace App\Providers;

use App\Html\CustomElements;
use Filament\Support\Assets\Js;
use Filapress\RichEditor\FPRichEditor;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
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

        // Delete out images when doing a migrate:fresh
        Event::listen(CommandStarting::class, function (CommandStarting $event) {
            if ($event->command === 'migrate:fresh') {
                \Storage::disk('public')->deleteDirectory('images');

            }
        });

        if (Vite::isRunningHot()) {
            FPRichEditor::$scriptSrc = Js::make('editor', Vite::asset('packages/rich-editor/resources/js/FPRichEditor.js'))->module();
        }

    }
}
