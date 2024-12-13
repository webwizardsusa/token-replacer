<x-layout :title="$post->title" :heading="false">
        <div class="flex flex-col lg:flex-row gap-8">
        <div class="post flex-1">
            @if(auth()->user())
                <div class="text-right">
                    <a href="{{ \URL::route('filament.admin.resources.posts.edit', ['record' => $post]) }}">Edit</a>
                </div>
            @endif
            <h2 class="text-3xl mb-2">{{ $post->title }}</h2>
            <div class="mb-4">
                <div>
                    By: {{ $post->user->name }}
                </div>
                <div>
                    {{ $post->published_at }}
                </div>
            </div>
                @if($post->image)

            <div class="mb-4">
                    {{ $post->image->render()->attributes(['link' => '__source__']) }}
            </div>
                @endif

                <div class="mb-6 italic text-lg">
                    {{ $post->description }}
                </div>
            <div class="mt-4">
                {{ $post->renderBody() }}
            </div>
        </div>
            <aside class="lg:w-[350px] w-full ">
                <h4 class="font-bold text-2xl mb-2">Latest</h4>
                @foreach($latest as $postitem)
                    <x-post-card :post="$postitem" class="border rounded-md overflow-hidden mb-4"/>
                    @endforeach
            </aside>
    </div>
</x-layout>

