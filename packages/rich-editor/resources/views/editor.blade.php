<?php
$statePath = $getStatePath();
$contentStyles = [];
if ($getMaxHeight()) {
    $contentStyles[] = 'max-height:' . $getMaxHeight();
    $contentStyles[] = 'overflow:auto';
}
if ($getMinHeight()) {
    $contentStyles[] = 'min-height:' . $getMinHeight();
}
$isHot = $getIsHot();
$scriptSrc = $getScriptSrc();
$scriptHot = $scriptSrc instanceof \Filament\Support\Assets\Js;
$externals = $getExternals();
?>
@if($scriptHot)
    @pushonce('scripts')
        <script src="{{ $scriptSrc->getSrc() }}" type="module"></script>
    @endpushonce
    @endif
<x-dynamic-component :component="$getFieldWrapperView()" :field="$field" class="relative z-0">

    <div
    >
        <div
            wire:ignore
            @if(!$scriptHot)
                x-ignore
                ax-load="visible"
                ax-load-src="{{ $scriptSrc}}"
            @endif
            x-data="FPRichEditor({
                state: $wire.{{ $applyStateBindingModifiers("entangle('{$statePath}')", isOptimisticallyLive: true) }},
                statePath: '{{ $getStatePath() }}',
                stickyToolbar: {{ $getStickyToolbar() ? "true" : "false"}},
                plugins: @js($getPlugins()),
                buttons: @js($getButtons()),
                withHelp: {{ $withHelp ? true : false}},
                placeholder: 'Write something special',
                pluginExternals: @js($externals)
            })"
            :class="{
                'fp-rich-editor rounded-lg shadow-sm': true,
                ' ring-1 ring-gray-950/10 dark:ring-white/20': !focused,
                'editor-has-focus': focused
                }"

            x-on:modal-action.window="modalAction($event)"

        >
            <div class="fp-rich-editor-toolbar" x-ref="toolbar">
            </div>
            <div


            >
                <div class="fp-rich-editor-content p-2" x-ref="content"
                    @style($contentStyles)
                ></div>
            </div>

        </div>
    </div>

{{--    @pushonce('scripts')--}}
{{--        @if(!empty($scripts['vite']))--}}
{{--        @vite($scripts['vite'])--}}
{{--        @endif--}}
{{--            @if(!empty($styles['vite']))--}}
{{--                @vite($styles['vite'])--}}
{{--            @endif--}}
{{--    @endpushonce--}}
</x-dynamic-component>
