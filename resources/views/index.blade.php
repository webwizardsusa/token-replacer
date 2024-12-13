<x-layout title="Latest">
    <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
        @foreach($posts as $post)
            <x-post-card :post="$post" class="border rounded-md overflow-hidden"/>
        @endforeach
    </div>

    <div class="my-4">
        {{ $posts->onEachSide(5)->links() }}
    </div>
</x-layout>
