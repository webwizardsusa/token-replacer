<?php
    $asset = \Filapress\Core\Assets\FilapressJs::getInstance('media-api', 'filapress/media')
    ?>
<div>
    <script>
        window.FPMediaBrowser = window.FPMediaBrowser || {};
    </script>
    {!! \Filapress\Core\Assets\FilapressJs::getInstance('media-api', 'filapress/media')->render() !!}

{{--    @vite(['packages/media/resources/js/MediaBrowserApi.js'])--}}
    @if($visible)
        <livewire:filapress-media-browser :options="$options"/>
    @endif
</div>

