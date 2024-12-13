@php
    $statePath = $getStatePath();
@endphp
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{
                state: $wire.{{ $applyStateBindingModifiers("entangle('{$statePath}')") }},
                statePath: '{{ $getStatePath() }}',
                types: @js($getTypes()),
                collection:@js($getCollection()),
                showBrowser() {

                   FPMediaBrowser.show(this.types, false, this.state)
                   .types(this.types)
                   .selected(this.state)
                   .collection(this.collection)
                   .then(result => {
                   if (!result) {
                   return;
                   }
                    result = result[0];
                     this.state = result && result.media ? result.media : null;
                   });
                }
            }">
        <div class="rounded ring-1 ring-gray-950/10 dark:ring-white/20 bg-white dark:bg-white/5">
            @if(!$getState())
            <button class="block w-full h-full p-4" x-on:click.prevent="showBrowser()">
                Click to browse or upload your {{ $getLabel() }}
            </button>
                @else
                <div
                    x-on:click.prevent="showBrowser()"
                >
                    <x-filapress-media-preview :media="$getMedia()" style="height:400px;" :allowFullscreen="false"/>
                </div>

            @endif
        </div>
    </div>
</x-dynamic-component>
