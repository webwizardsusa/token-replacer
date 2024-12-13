<?php

namespace Webwizardsusa\OEmbed\Providers;

class TiktokProvider extends AbstractOembedProvider
{
    protected bool $renderHtml = true;

    protected array $regexes = [
        '#https?://(www\.)?tiktok\.com/.*/video/.*#i',
        '#https?://(www\.)?tiktok\.com/@.*#i',
    ];

    protected string $oembedUrl = 'https://www.tiktok.com/oembed';

    public function name(): string
    {
        return 'tiktok';
    }
}
