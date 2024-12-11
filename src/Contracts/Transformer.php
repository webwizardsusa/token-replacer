<?php

namespace Webwizardsusa\TokenReplace\Contracts;

use Webwizardsusa\TokenReplace\TokenReplacer;

interface Transformer
{
    public function process(string $options, TokenReplacer $replacer): string;
}
