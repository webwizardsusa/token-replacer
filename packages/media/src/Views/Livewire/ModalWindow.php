<?php

namespace Filapress\Media\Views\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
class ModalWindow extends Component
{
    public bool $visible = false;

    public array $options = [];

    public function mount()
    {


    }

    #[On('open-filapress-media-browser')]
    public function openModal($options): void
    {
        $this->options = array_merge([
            'inline' => false,
            'types' => [],
            'selected' => null,
        ], $options);

        if ($this->options['selected'] && ! is_array($this->options['selected'])) {
            $this->options['selected'] = [
                'media' => $this->options['selected'],
            ];
        }
        $this->visible = true;
    }

    #[On('close-filapress-media-browser')]
    public function closeModal()
    {
        $this->visible = false;
        $this->dispatch('filapress-media-browser-results');
    }

    #[On('filapress-media-browser-update')]
    public function sendResults($data)
    {
        $this->dispatch('filapress-media-browser-results', $data);
        $this->visible = false;
    }

    public function render()
    {
        return view('filapress-media::components.browser.modal');
    }
}
