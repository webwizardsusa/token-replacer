<?php

namespace Filapress\Media\Views\Livewire;
use Arr;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filapress\Media\Filament\Components\Form\MediaInfoField;
use Filapress\Media\Models\FilapressMedia;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * @property Form $form
 */
class BrowserInsertPanel extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public FilapressMedia $media;

    public array $data = [];

    public array $options;

    public function render(): View
    {
        return view('filapress-media::components.browser.insert');
    }

    public function mount(FilapressMedia $media, array $options): void
    {
        $this->options = $options;
        $attributes = Arr::get($options, 'attributes', []);
        $this->data = $attributes;
        $this->media = $media;
        $attributes = $this->media->getType()->prepareInsertAttributes($this->media, $attributes);
        $this->form->fill($attributes);
    }

    public function form(Form $form): Form
    {
        $form->statePath('data');
        $schema = [
            MediaInfoField::make('media')
                ->data($this->media->getType()->mediaInfo($this->media)),
        ];
        if ($this->options['inline']) {
            $typeSchema = $this->media->getType()->insertForm($this->media, $form);
            foreach ($typeSchema as $field) {
                $schema[] = $field;
            }
        }
        $form->schema($schema);

        return $form;
    }

    public function cancel(): void
    {
        $this->dispatch('media-browser-cancel');
    }

    public function insert(): void
    {
        $data = Arr::except($this->form->getState(), ['media']);
        $data['media'] = $this->media->id;
        $data = $this->media->getType()->onInsert($this->media, $data, $this->options);
        $this->dispatch('filapress-media-browser-update', $data);
    }

    public function showRemove(): bool
    {
        if ($this->options['inline']) {
            return false;
        }

        $selected = Arr::get($this->options, 'selected.media');
        if ($selected && $selected === $this->media->id) {
            return true;
        }

        return false;
    }

    public function remove(): void
    {
        $this->dispatch('filapress-media-browser-update', null);
    }
}
