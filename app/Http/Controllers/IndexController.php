<?php

namespace App\Http\Controllers;

use App\Models\Post;

class IndexController
{

    public function __invoke() {
        return view('index', ['posts' => Post::paginate(10)]);
    }
}
