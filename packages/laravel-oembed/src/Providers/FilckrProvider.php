<?php

namespace Webwizardsusa\OEmbed\Providers;

class FilckrProvider extends AbstractOembedProvider
{
    protected bool $renderHtml = true;

    protected array $regexes = [
        '#https?://(www\.)?flickr\.com/.*#i',
        '#https?://flic\.kr/.*#i',
    ];

    protected string $oembedUrl = 'https://www.flickr.com/services/oembed.json/';

    public function name(): string
    {
        return 'flickr';
    }
}
