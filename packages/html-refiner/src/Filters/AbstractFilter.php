<?php

namespace Webwizardsusa\HtmlRefiner\Filters;

use Webwizardsusa\HtmlRefiner\RefinerDefinition;

abstract class AbstractFilter
{

    public function __construct() {

    }

    public static function make(): static
    {
        return new static();
    }

    public function preProcess(string $html, RefinerDefinition $definition): string
    {
        return $html;
    }

    abstract public function process(string $html, RefinerDefinition $definition): string;

    public function postProcess(string $html, RefinerDefinition $definition): string
    {
        return $html;
    }
}
