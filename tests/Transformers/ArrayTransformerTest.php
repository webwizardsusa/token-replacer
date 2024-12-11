<?php

namespace Webwizardsusa\TokenReplace\Tests\Transformers;

use Webwizardsusa\TokenReplace\Tests\TestCase;
use Webwizardsusa\TokenReplace\TokenReplacer;
use Webwizardsusa\TokenReplace\Transformers\ArrayTransformer;

class ArrayTransformerTest extends TestCase
{
    /** @test **/
    public function it_extracts_items_from_an_array()
    {
        $str = 'The quick brown {{animal:jumper}} jumped over the lazy {{animal:target}}';
        $transformer = TokenReplacer::from($str)
            ->with('animal', new ArrayTransformer([
                'jumper' => 'fox',
                'target' => 'dog',
            ]));

        $this->assertEquals('The quick brown fox jumped over the lazy dog', $transformer->transform());
    }

    /** @test **/
    public function it_removes_missing_array_values()
    {
        $str = 'The quick brown {{animal:jumper}} jumped over the lazy {{animal:target}}';
        $transformer = TokenReplacer::from($str)
            ->with('animal', new ArrayTransformer([
                'jumper' => 'fox',
            ]))->removeEmpty(true);


        $this->assertEquals('The quick brown fox jumped over the lazy ', $transformer->transform());
    }
}
