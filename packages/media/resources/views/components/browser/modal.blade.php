<div>
    <script>
        window.FPMediaBrowser = window.FPMediaBrowser || {};

    </script>
    @vite(['packages/media/resources/js/MediaBrowserApi.js'])
    @if($visible)
        <livewire:filapress-media-browser :options="$options"/>

    @endif
</div>

