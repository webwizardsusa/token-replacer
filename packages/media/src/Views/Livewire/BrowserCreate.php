<?php

namespace Filapress\Media\Views\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Support\Enums\Alignment;
use Filapress\Media\MediaType;
use Filapress\Media\MediaTypes;
use Illuminate\View\View;
use Livewire\Component;

class BrowserCreate extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithFormActions;
    use InteractsWithForms;

    public array $data = [];

    public string $type;

    public bool $hasFormProcessingLoadingIndicator = true;

    public ?string $collection = null;

    public function mount($type, $collection): void
    {
        $this->type = $type;
        $this->collection = $collection;
        $this->form->fill();
    }

    public function getType(): MediaType
    {
        return app(MediaTypes::class)->get($this->type);
    }

    public function form(Form $form): Form
    {
        $form->statePath('data');
        $schema = $this->getType()->form($form);
        $form->schema($schema);

        return $form;
    }

    public function create()
    {
        $data = $this->form->getState();
        $data['collection'] = $this->collection;
        $media = $this->getType()->create($data);
        $this->dispatch('media-browser-create', $media->id);
    }

    public function cancel()
    {
        $this->dispatch('media-browser-close-create');
    }

    public function formActions()
    {
        return [
            Action::make('create')
                ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
                ->action(function () {
                    dd('OK');
                })
                ->keyBindings(['mod+s']),
        ];
    }

    public function render(): View
    {
        return view('filapress-media::components.browser.create');
    }

    public function getFormActionsAlignment(): string|Alignment
    {
        return Alignment::Start;
    }

    public function areFormActionsSticky(): bool
    {
        return false;
    }
}
