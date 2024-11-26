<?php

use App\Html\CustomElements;

it('returns a successful response', function () {
    $html='<oembed src="https://www.youtube.com/watch?v=hXWeiQYxT6k"></oembed>'.PHP_EOL.PHP_EOL . 'jamie';


    $refiner = \App\Html\PostDefinition::for($html)
        ->context('editor')
        ->parse();
    dd($refiner);
    $elements = app(CustomElements::class);
    dd($elements);
    $response = $this->get('/');

    $response->assertStatus(200);
});
