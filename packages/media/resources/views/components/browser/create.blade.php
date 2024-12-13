<div class="p-4"
     x-on:keydown.escape.window="$wire.call('cancel')"
>
    <x-filament-panels::form
        id="form"
        :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
        wire:submit="create"
    >
    {{ $this->form }}
    <div class="mt-2">
        <x-filament::button color="primary" wire:click.prevent="create" type="submit" :form="$this->form">Create</x-filament::button>
        <x-filament::button color="gray" wire:click="cancel">Cancel</x-filament::button>
    </div>
    </x-filament-panels::form>
</div>
