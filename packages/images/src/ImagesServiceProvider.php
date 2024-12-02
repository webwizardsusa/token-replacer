<?php

namespace Filapress\Images;


use Illuminate\Support\ServiceProvider;

class ImagesServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->mergeConfigFrom(__DIR__ . '/../config/images.php', 'filapress.images');
    }
}
