<?php

namespace Filapress\Core;

use Filament\Contracts\Plugin;

class FilapressPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filapress';
    }

    public function register(\Filament\Panel $panel): void
    {
        event(new \Filapress\Core\Events\FilapressPluginRegisterEvent($panel));
    }

    public function boot(\Filament\Panel $panel): void {}
}
