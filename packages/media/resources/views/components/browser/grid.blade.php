<div class="flex-1 overflow-y-auto">
    <div class="media-browser-filters">
        <div class="filters-form">
            {{ $this->form }}
        </div>
        @if($createTypes->count())
            <x-filament::dropdown
                :placement="'bottom-start'"
                :teleport="true"
            >
                <x-slot name="trigger">
                    <x-filament::button size="sm" icon="heroicon-o-plus"
                                        class="browser-create-button" tooltip="Create"/>
                </x-slot>
                <x-filament::dropdown.list>
                    @foreach($createTypes as $type)
                        <x-filament::button color="null"
                                            wire:click="setCreate('{{ $type->name() }}')"
                        >Create {{ $type->label() }}</x-filament::button>
                    @endforeach
                </x-filament::dropdown.list>
            </x-filament::dropdown>
        @endif
    </div>
    @php
        $items = $this->getItems();
    @endphp
    <div
        @class([
        'media-browser-items' => true,
        'is-empty' => $items->isEmpty()
        ])
    >

        @if($items->isEmpty())
            <p>No items found</p>
        @else
            @foreach($items as $record)
                <div
                    wire:click="select('{{ $record->id }}')"
                    class="cursor-pointer rounded-xl h-full overflow-hidden bg-gray-100 dark:bg-gray-950/50 relative filapress-media-table-record">
                    <img
                        src="{{ $record->getType()->thumbnailUrl($record) }}"
                        alt="{{ $record->title }}"
                        @class([
                            'h-full object-cover w-full',
                        ])
                    />
                    <div
                        class="absolute bg-black/50 inset-x-0 top-0 text-sm p-1 text-white bg-gradient-to-t from-black/80 to-transparent "
                    >
                        <p class="truncate">{{ $record->title }}</p>
                    </div>
                    <div
                        class="absolute bg-black/50 inset-x-0 bottom-0 p-1 text-sm text-white bg-gradient-to-t from-black/80 to-transparent"
                    >
                        <p class="truncate">{{ $record->getType()->label() }}
                            @if($record->width && $record->height)
                                &nbsp({{ $record->width }}x{{ $record->height }})
                            @endif
                        </p>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <div class="p-4 border-t border-t-gray-200 dark:border-t-white/10">
        <x-filament::pagination :paginator="$items"/>
    </div>
</div>
