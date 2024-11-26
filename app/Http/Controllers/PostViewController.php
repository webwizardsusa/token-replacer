<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostViewController
{

    public function __invoke(Post $post) {
        return view('post', ['post' => $post]);
    }
}
