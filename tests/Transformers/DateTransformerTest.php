<?php

namespace Webwizardsusa\TokenReplace\Tests\Transformers;

use Webwizardsusa\TokenReplace\Exceptions\InvalidTransformerOptionsException;
use Webwizardsusa\TokenReplace\Tests\TestCase;
use Webwizardsusa\TokenReplace\Transformers\DateTransformer;

class DateTransformerTest extends TestCase
{
    /** @test **/
    public function it_transforms_a_date()
    {
        $transformer = new \Webwizardsusa\TokenReplace\TokenReplacer('replace a {{ date:m }}/{{ date:d }}/{{ date:y }} date token');
        $transformer->with('date', DateTransformer::class);
        $this->assertEquals('replace a '. implode('/', [
                date('m'),
                date('d'),
                date('y'),
            ]) . ' date token', (string) $transformer);
    }

    /** @test **/
    public function it_requires_a_format_option()
    {
        $transformer = new \Webwizardsusa\TokenReplace\TokenReplacer('{{ date }}');
        $this->expectException(InvalidTransformerOptionsException::class);
        $transformer->with('date', DateTransformer::class);
        $transformer->transform();
    }
}
