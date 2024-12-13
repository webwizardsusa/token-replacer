<?php

namespace Webwizardsusa\OEmbed\Providers;

class DailymotionProvider extends AbstractOembedProvider
{
    protected array $regexes = [
        '#https?://(www\.)?dailymotion\.com/.*#i',
        '#https?://dai\.ly/.*#i',
    ];

    protected string $oembedUrl = 'https://www.dailymotion.com/services/oembed';

    public function name(): string
    {
        return 'dailymotion';
    }
}
