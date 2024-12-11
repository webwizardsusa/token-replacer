<?php

namespace Webwizardsusa\TokenReplace\Tests;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        \Webwizardsusa\TokenReplace\TokenReplacer::$defaultTransformers = [
            'date' => \Webwizardsusa\TokenReplace\Transformers\DateTransformer::class,
        ];
    }
}
