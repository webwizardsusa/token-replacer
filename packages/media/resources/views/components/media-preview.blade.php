<div
    {{ $attributes->class([
'filapress-media-preview filapress-media-type-' . $media->type .'overflow-hidden relative h-full' => true
]) }}
    x-data="{ fullscreen: false }"
    x-bind:class="{
        'fixed inset-0 z-50': fullscreen,
        'relative': !fullscreen,
    }"
    x-on:keydown.escape.window="fullscreen = false"
>
    <div class="absolute inset-0 max-h-full">
        <div class=" media-item max-h-full h-full w-full max-w-full flex justify-center items-center">
            {{ $media->render(preview: true) }}
        </div>
        <div
            class="absolute bg-black/50 inset-x-0 top-0 text-sm p-1 text-white bg-gradient-to-t from-black/80 to-transparent "
        >
            <p class="truncate">{{ $media->title }}</p>
        </div>
        <div
            class="absolute bg-black/50 inset-x-0 bottom-0 p-1 text-sm text-white bg-gradient-to-t from-black/80 to-transparent"
        >
            <p class="truncate">{{ $media->getType()->label() }}
                @if($media->width && $media->height)
                    &nbsp({{ $media->width }}x{{ $media->height }})
                @endif
            </p>
        </div>
    </div>
    @if($allowFullscreen)
    <button x-on:click.prevent.stop="fullscreen = !fullscreen" class="absolute top-0 right-0 w-8 h-8 m-2">
        <x-filament::icon icon="heroicon-m-arrows-pointing-out"/>
    </button>
        @endif
</div>
