<?php
$state = $getState();
if ($state) {
    $oembed = \Webwizardsusa\OEmbed\OEmbedResponse::hydrate(json_decode($state, true));

}

$url = \Illuminate\Support\Facades\URL::route('preview-oembed', ['url' => $oembed->getUrl()]);
?>
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <iframe src="{{ $url }}" id="oembed-preview-frame" style="width: 100%;overflow:hidden; border:none;background: transparent;"
            allowtransparency="true"
            scrolling="no"
    ></iframe>


</x-dynamic-component>
