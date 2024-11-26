<?php

namespace Filapress\RichEditor\Actions;

use Filament\Forms\Components\Actions\Action;

class EditorAction  extends Action
{

    public function sendActionToEditor(string $action, array $data = []): void {
        $arguments = $this->getArguments();
        $coordinates = [];
        if (\Arr::has($arguments, '_coordinates')) {
            $coordinates = \Arr::get($arguments, '_coordinates', []);
        }
        $component = $this->getComponent();
        $component->getLivewire()->dispatch(
            event: 'modalAction',
            statePath: $component->getStatePath(),
            action:$action,
            coordinates: $coordinates,
            args: $data,
        );

        $component->state($component->getState());
    }

}
