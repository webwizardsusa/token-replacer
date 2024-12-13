@props([
    'post'
])
<div {{ $attributes->class([
    'post-card'
]) }}>
    <div class="card-image">
        @if($post->image)
            {{ $post->image->render('card', ['link' => URL::route('post.view', $post), 'target' => null], false) }}
        @endif
    </div>
    <div class="p-4">
        <h3 class="font-bold text-2xl mb-2">
            <a href="{{ URL::route('post.view', $post) }}" class="block decoration-none hover:underline" >
                {{ $post->title }}
            </a>
        </h3>
        <div class="mb-2 text-gray-500">
            {{ $post->published_at->format('Y/m/d') }}
        </div>
        <div>
            {{ $post->description }}
        </div>

    </div>
    </div>
