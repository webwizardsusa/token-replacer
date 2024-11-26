<div>

    @foreach($items as $item)
        <div class="mb-4">

            {{ $item->render() }}
        </div>
    @endforeach
</div>
