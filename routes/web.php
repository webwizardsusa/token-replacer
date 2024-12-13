<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', \App\Http\Controllers\IndexController::class)->name('home');
Route::get('/post/{post}', \App\Http\Controllers\PostViewController::class)->name('post.view');
Route::group(['middleware' => ['auth']], function () {
    Route::post('/editor/oembed', \App\Http\Controllers\OembedLookupController::class)->name('editor.oembed');
});

Route::get('preview-oembed', function (Request $request) {
    $url = $request->get('url');
    $oembed = app(\Webwizardsusa\OEmbed\OEmbed::class)->fromUrl($url);
    if (! $oembed) {
        abort(404);
    }

    return view('oembed-preview', ['oembed' => $oembed]);
})->name('preview-oembed');
