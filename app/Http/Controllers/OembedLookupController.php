<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Webwizardsusa\OEmbed\OEmbed;

class OembedLookupController
{
    public function __invoke(Request $request)
    {
        $urls = $request->get('urls', []);
        $service = app(OEmbed::class);

        return response()->json($request->all());
        dd($request->all());
    }
}
