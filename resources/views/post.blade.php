
<x-layout :title="$post->title" :heading="false">
    <div class="post">
        <h2 class="text-3xl mb-4">{{ $post->title }}</h2>
        <div>
            {{ $post->renderBody() }}
        </div>
    </div>
</x-layout>

