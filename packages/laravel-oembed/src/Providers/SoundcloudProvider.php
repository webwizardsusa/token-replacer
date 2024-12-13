<?php

namespace Webwizardsusa\OEmbed\Providers;

class SoundcloudProvider extends AbstractOembedProvider
{
    protected bool $renderHtml = true;

    protected array $regexes = [
        '#https?://(www\.)?soundcloud\.com/.*#i',
    ];

    protected string $oembedUrl = 'https://soundcloud.com/oembed.json/';

    public function name(): string
    {
        return 'soundcloud';
    }
}
