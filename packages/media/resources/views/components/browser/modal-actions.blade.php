<div>
    @foreach ($actions as $action)
        {{ $action(['record' => $media]) }}
    @endforeach
</div>
