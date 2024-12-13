<?php

namespace Filapress\Core\Events;

use Filament\Panel;

class FilapressPluginRegisterEvent
{
    public Panel $panel;

    public function __construct(Panel $panel)
    {
        $this->panel = $panel;
    }
}
