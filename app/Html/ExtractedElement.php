<?php

namespace App\Html;

class ExtractedElement
{
    protected string $tag;

    protected array $attributes;

    protected string $content;

    public function __construct(string $tag, array $attributes, string $content)
    {

        $this->tag = $tag;
        $this->attributes = $attributes;
        $this->content = $content;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function setTag(string $tag): ExtractedElement
    {
        $this->tag = $tag;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): ExtractedElement
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): ExtractedElement
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
        $attributes = [];
        foreach ($this->attributes as $key => $value) {
            $attributes[] = $key.'="'.$value.'"';
        }
        $output = '<'.$this->tag;
        if (count($attributes)) {
            $output .= ' '.implode(' ', $attributes);
        }
        if ($this->content) {
            $output .= '>'.$this->content.'</'.$this->tag.'>';
        } else {
            $output .= ' />';
        }

        return $output;
    }

    public function __toString()
    {
        return $this->render();
    }
}
