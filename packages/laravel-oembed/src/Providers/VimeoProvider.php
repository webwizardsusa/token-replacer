<?php

namespace Webwizardsusa\OEmbed\Providers;

class VimeoProvider extends AbstractOembedProvider
{
    protected array $regexes = [
        '#https?://(www\.)?vimeo\.com/.*#i',
    ];

    protected string $oembedUrl = 'https://vimeo.com/api/oembed.json';

    public function name(): string
    {
        return 'vimeo';
    }
}
