<?php

namespace Webwizardsusa\TokenReplace\Tests\Transformers;

use Webwizardsusa\TokenReplace\Tests\TestCase;
use Webwizardsusa\TokenReplace\TokenReplacer;
use Webwizardsusa\TokenReplace\Transformers\ObjectTransformer;

class ObjectTransformerTest extends TestCase
{
    /** @test **/
    public function it_extracts_items_from_an_array()
    {
        $str = 'The quick brown {{animal:jumper}} jumped over the lazy {{animal:target}}';
        $obj = new \stdClass();
        $obj->jumper = 'fox';
        $obj->target = 'dog';
        $transformer = TokenReplacer::from($str)
            ->with('animal', new ObjectTransformer($obj));

        $this->assertEquals('The quick brown fox jumped over the lazy dog', $transformer->transform());
    }
}
