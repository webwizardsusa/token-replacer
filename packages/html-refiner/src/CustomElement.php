<?php

namespace Webwizardsusa\HtmlRefiner;

abstract class CustomElement
{
    protected bool $inline = false;

    protected bool $selfClosing = false;

    protected array $attributes = [];

    abstract public function tag(): string;

    public static function make(): static
    {
        return new static;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function preFilter(string $html, RefinerDefinition $definition): string
    {
        return $html;
    }

    public function render(string $html, RefinerDefinition $definition): string
    {
        return $html;
    }

    public function isInline(): bool
    {
        return $this->inline;
    }

    public function isSelfClosing(): bool
    {
        return $this->selfClosing;
    }

    /**
     * @return array | ExtractedCustomElement[]
     */
    public function extract(string $html): array
    {
        $tag = preg_quote($this->tag());
        $pattern = '/<'.$tag.'\b([^>]*?)\/?>(.*?)<\/'.$tag.'>|<'.$tag.'\b([^>]*?)\/>/';
        $results = [];
        if (preg_match_all($pattern, $html, $matches)) {
            foreach ($matches[0] as $key => $search) {
                $attrString = trim($matches[1][$key] ?: $matches[3][$key]);
                $content = $matches[2][$key];
                $attributes = [];
                if (preg_match_all('/(\w+)=["\']([^"\']+)["\']/', $attrString, $attrMatches, PREG_SET_ORDER)) {
                    foreach ($attrMatches as $item) {
                        $value = $item[2];
                        if (is_string($value)) {
                            $value = html_entity_decode($value);
                        }
                        $attributes[$item[1]] = $value;
                    }
                }
                $results[] = new ExtractedCustomElement($search, $this->tag(), $attributes, $content, $this->selfClosing);
            }
        }

        return $results;
    }
}
