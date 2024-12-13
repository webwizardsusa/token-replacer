<?php

namespace Filapress\Core;

use Filapress\Core\Auth\PermissionsRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->app->singleton(PermissionsRepository::class);
    }

    public function boot(): void
    {
        Gate::before(function ($user, $ability, ...$params) {
            if (str_starts_with($ability, 'filapress.')) {
                $permission = \Str::after($ability, 'filapress.');
                if (! $user->hasFilapressPermission($permission)) {
                    return false;
                }
            }

            return null;
        });
    }
}
