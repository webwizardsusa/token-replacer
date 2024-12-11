<?php

namespace Webwizardsusa\TokenReplace\Transformers\Laravel;

use Webwizardsusa\TokenReplace\Exceptions\InvalidTransformerOptionsException;
use Webwizardsusa\TokenReplace\TokenReplacer;
use Webwizardsusa\TokenReplace\Transformers\Transformer;
use Illuminate\Support\Arr;

class DotArrayTransformer extends Transformer
{
    protected array $inputArray;

    public function __construct(array $inputArray)
    {
        $this->inputArray = $inputArray;
    }

    public function process(string $options, TokenReplacer $replacer): string
    {
        if (! $options) {
            throw new InvalidTransformerOptionsException('DotArrayTransformer option required');
        }

        return Arr::get($this->inputArray, $options, '');
    }
}
