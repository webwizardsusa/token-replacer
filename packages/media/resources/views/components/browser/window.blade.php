@php
/** @var \Illuminate\Support\Collection|\Filapress\Media\MediaType[] $createTypes */
$createTypes = $this->createTypes()
@endphp
<div class="filapress-media-browser" x-data="{}"
     @if(!$selected && !$createType)
         x-on:keydown.escape.window="$wire.call('closeModal')"
    @endif
>
    <div class="media-browser-header">
        <h4>
            {{ $this->getTitle() }}
        </h4>

        <div class="flex-1"></div>
        <x-filament::icon-button
            color="gray"
            icon="heroicon-o-x-mark"
            icon-alias="modal.close-button"
            icon-size="lg"
            :label="__('filament::components/modal.actions.close.label')"
            tabindex="-1"
            wire:click="closeModal()"
            class="fi-modal-close-btn"
        />
    </div>
    <div class="media-browser-content">

        @if($createType)
            <livewire:filapress-media-browser-create :type="$createType" :collection="$collection">

        @elseif($selected)
            <livewire:filapress-media-insert :media="$selected"
                                             :options="$options"></livewire:filapress-media-insert>
        @else
            @include('filapress-media::components.browser.grid')
        @endif
    </div>

</div>

