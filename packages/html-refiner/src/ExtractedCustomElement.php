<?php

namespace Webwizardsusa\HtmlRefiner;

class ExtractedCustomElement
{
    protected string $tag;

    protected array $attributes;

    protected string $content;

    protected string $search;

    protected bool $selfClosing;

    public function __construct(string $search, string $tag, array $attributes, string $content, bool $selfClosing = false)
    {

        $this->tag = $tag;
        $this->attributes = $attributes;
        $this->content = $content;
        $this->search = $search;
        $this->selfClosing = $selfClosing;
    }

    public function raw(): string
    {
        return $this->search;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function setTag(string $tag): static
    {
        $this->tag = $tag;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->attributes);
    }

    public function getAttribute(string $attribute, mixed $default = null): mixed
    {
        return $this->attributes[$attribute] ?? $default;
    }

    public function removeAttribute(string $attribute): static
    {
        if ($this->hasAttribute($attribute)) {
            unset($this->attributes[$attribute]);
        }

        return $this;
    }

    public function setAttribute(string $attribute, $value): static
    {
        $this->attributes[$attribute] = $value;

        return $this;
    }

    public function render(): string
    {
        return new Element($this->tag, $this->attributes, $this->content, $this->selfClosing);
    }
}
