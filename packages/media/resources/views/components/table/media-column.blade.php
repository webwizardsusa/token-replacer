@php
    $record = $getRecord();
@endphp
<div
    {{ $attributes->merge($getExtraAttributes())
    ->merge(['data-record-id' => $record->id])
    ->class(['filapress-media-grid-column relative rounded-t-xl overflow-hidden aspect-video']) }}
>


    <div class="rounded-t-xl h-full overflow-hidden bg-gray-100 dark:bg-gray-950/50 relative">
        <img
            src="{{ $record->getType()->thumbnailUrl($record) }}"
            alt="{{ $record->title }}"
            @class([
                'h-full object-cover w-full',
            ])
        />
        <div
            class="absolute bg-black/50 inset-x-0 top-1 flex items-center justify-between px-1.5 pt-10 pb-1.5 text-sm text-white bg-gradient-to-t from-black/80 to-transparent gap-3"
        >
            <p class="truncate">{{ $record->title }}</p>
        </div>
        <div
            class="absolute bg-black/50 inset-x-0 bottom-0 mb-2 flex items-center justify-between px-1.5 pt-10 pb-1.5 text-sm text-white bg-gradient-to-t from-black/80 to-transparent gap-3"
        >
            <p class="truncate">{{ $record->getType()->label() }}
                @if($record->width && $record->height)
                    &nbsp({{ $record->width }}x{{ $record->height }})
                    @endif
            </p>
        </div>
    </div>
</div>
