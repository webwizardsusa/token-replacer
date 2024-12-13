<?php

namespace Filapress\Media;

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filapress\Core\Assets\FilapressJs;
use Filapress\Core\Events\FilapressPluginRegisterEvent;
use Filapress\Core\Events\RegisterPermissionsEvent;
use Filapress\Media\Filament\FilapressMediaResource;
use Filapress\Media\Views\Livewire\BrowserCreate;
use Filapress\Media\Views\Livewire\BrowserInsertPanel;
use Filapress\Media\Views\Livewire\MediaBrowser;
use Filapress\Media\Views\Livewire\ModalWindow;
use Filapress\Media\Images\ImageFactory;
use Filapress\Media\Listeners\MediaPermissionsListener;
use Filapress\Media\Models\FilapressMedia;
use Filapress\Media\Policies\FilapressMediaPolicy;
use Filapress\Media\Views\Components\MediaPreviewComponent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class MediaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->mergeConfigFrom(__DIR__.'/../config/media.php', 'filapress.media');
        $this->loadviewsFrom(__DIR__.'/../resources/views', 'filapress-media');
        $this->app->singleton(MediaTypes::class, function () {
            return new MediaTypes(config('filapress.media.types', []));
        });

        $this->app->singleton(ImageFactory::class, function () {
            return new ImageFactory(config('filapress.media.image_driver', 'gd'));
        });

        $this->app->singleton(MediaCollections::class, function(){
            return new MediaCollections(config('filapress.media.collections', []));
        });

        $this->app->singleton(ImageVariants::class, function(){
            return new ImageVariants(config('filapress.media.image_variants', []));
        });

        Event::listen(FilapressPluginRegisterEvent::class, function (FilapressPluginRegisterEvent $event) {
            if (!request()->hasHeader('x-livewire')) {
                $event->panel->renderHook('panels::body.end', fn () => Livewire::mount('filapress-media-browser-window'));
            }
        });
    }

    public function boot(): void
    {
        Livewire::component('filapress-media-browser', MediaBrowser::class);
        Livewire::component('filapress-media-insert', BrowserInsertPanel::class);
        Livewire::component('filapress-media-browser-window', ModalWindow::class);
        Livewire::component('filapress-media-browser-create', BrowserCreate::class);
        \Blade::component('filapress-media-preview', MediaPreviewComponent::class);
        Event::listen(RegisterPermissionsEvent::class, MediaPermissionsListener::class);
        Gate::policy(FilapressMedia::class, config('filapress.media.policy', FilapressMediaPolicy::class));
        FilamentAsset::register([
            FilapressJs::make('media-api', __DIR__.'/../dist/media-api.js')
                ->dev(__DIR__ . '/../resources/js/MediaBrowserApi.js'),
            FilapressJs::make('media-editor-plugin', __DIR__.'/../dist/media-editor-plugin.js')
                ->loadedOnRequest(true)
                ->dev(__DIR__ . '/../resources/js/RichEditorMediaPlugin.js'),
        ], 'filapress/media');
    }
}
