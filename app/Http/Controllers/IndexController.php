<?php

namespace App\Http\Controllers;

use App\Models\Post;

class IndexController
{
    public function __invoke()
    {
        return view('index', ['posts' => Post::with('user', 'image', 'image.variants')
            ->where('published', true)
            ->orderBy('published_at', 'desc')
            ->paginate(12)]);
    }
}
