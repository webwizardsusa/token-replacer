
<x-layout title="Latest">
    @foreach($posts as $post)
        <div class="mb-4">
            <a href="{{ URL::route('post.view', $post) }}" class="font-bold text-2xl">
                {{ $post->title }}
            </a>
        </div>
        @endforeach
    <div class="mb-4">
        {{ $posts->onEachSide(5)->links() }}
    </div>
</x-layout>
