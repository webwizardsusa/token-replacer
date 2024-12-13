@php
$extraClasses = [];
if ($align) {
    $extraClasses[] = 'media-align-' . $align;
}
@endphp
@if($preview)
    <a href="{{ $src }}" target="{{ $target }}">
        <img src="{{ $src }}" alt="{{ $alt }}" width="{{ $width }}" height="{{ $height }}"

        />
    </a>
@else
    <div class="filapress-media filapress-media-image {{ implode(' ', $extraClasses) }}">
        <figure>
            @if($link)
                <a href="{{ $link }}" target="{{ $target }}">
                    @endif
                    <picture>
                        @foreach($sizes as $size)
                            <source srcset="{{ $size['url'] }}" media="(max-width: {{ $size['maxWidth'] }}px)">
                        @endforeach

                        <img srcset="{{ $src }}" alt="{{$alt}}" width="{{ $width }}" height="{{ $height }}"/>

                    </picture>
                    @if($link)
                </a>
            @endif
            @if($caption)
                <figcaption>{{ html_entity_decode($caption) }}</figcaption>
            @endif
        </figure>
    </div>

@endif
