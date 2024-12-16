<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostViewController
{
    public function __invoke(Post $post)
    {

        $latestPosts = Post::with('user', 'image', 'image.variants')
            ->where('id', '<>', $post->id)
            ->where('published', true)
            ->orderBy('published_at', 'desc')
            ->limit(3);

        return view('post', [
            'post' => $post,
            'latest' => $latestPosts->get(),
        ]);
    }
}
