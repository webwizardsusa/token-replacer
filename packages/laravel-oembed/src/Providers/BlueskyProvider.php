<?php

namespace Webwizardsusa\OEmbed\Providers;

class BlueskyProvider extends AbstractOembedProvider
{
    protected bool $renderHtml = true;

    protected array $regexes = [
        '#https?://bsky.app/profile/.*/post/.*#i',
    ];

    protected string $oembedUrl = 'https://embed.bsky.app/oembed';

    public function name(): string
    {
        return 'bluesky';
    }
}