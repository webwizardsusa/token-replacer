<div class="filapress-media-insert-window absolute inset-0"
     x-on:keydown.escape.window="$wire.call('cancel')"
>


    <div class="preview-panel">
        <x-filapress-media-preview :media="$media" />
    </div>
    <div class="insert-form">
        <div class="mb-4">
            {{ $this->form }}
        </div>
        <div class="flex gap-4">
            <x-filament::button color="primary" wire:click="insert">Insert</x-filament::button>
            @if($this->showRemove())
                <x-filament::button color="danger" wire:click="remove">Remove</x-filament::button>
            @endif
            <div class="flex-1"></div>
            <x-filament::button color="gray" wire:click="cancel">Back</x-filament::button>
        </div>
    </div>
</div>
