<?php

namespace Webwizardsusa\TokenReplace\Tests\Transformers;

use Webwizardsusa\TokenReplace\Tests\TestCase;
use Webwizardsusa\TokenReplace\TokenReplacer;
use Webwizardsusa\TokenReplace\Transformers\UrlTransformer;

class UrlTransformerTest extends TestCase
{
    /** @test **/
    public function is_extract_from_a_file_path()
    {
        $url = 'https://example.com/index.html';
        $replacer = TokenReplacer::from('{{ url:path }} is located at {{ url:scheme }}://{{ url:host }}')
            ->with('url', new UrlTransformer($url));

        $this->assertEquals('/index.html is located at https://example.com', $replacer->transform());
    }
}
