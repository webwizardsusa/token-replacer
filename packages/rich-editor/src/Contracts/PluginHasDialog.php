<?php

namespace Filapress\RichEditor\Contracts;

use Filament\Forms\Components\Actions\Action;

interface PluginHasDialog
{
    public function dialog(): Action;
}
