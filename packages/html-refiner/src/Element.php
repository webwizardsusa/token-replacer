<?php

namespace Webwizardsusa\HtmlRefiner;

class Element
{

    /**
     * @var mixed|string
     */
    protected mixed $content;
    protected string $tag;
    /**
     * @var array|mixed
     */
    protected mixed $attributes;
    protected bool $selfClosing;

    public function __construct(string $tag, $attributes = [], $content = '', bool $selfClosing = false)
    {
        $this->tag = $tag;
        $this->attributes = $attributes;
        $this->content = $content;
        $this->selfClosing = $selfClosing;
    }

    public function getContent(): mixed
    {
        return $this->content;
    }

    public function setContent(mixed $content): Element
    {
        $this->content = $content;
        return $this;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function setTag(string $tag): Element
    {
        $this->tag = $tag;
        return $this;
    }

    public function getAttributes(): mixed
    {
        return $this->attributes;
    }

    public function setAttributes(mixed $attributes): Element
    {
        $this->attributes = $attributes;
        return $this;
    }


    public function render(): string
    {
        $attributes = [];
        foreach($this->attributes as $key=>$value) {
            if (is_bool($value)) {
                if ($value) {
                    $attributes[] = $key;
                } else {
                    $attributes[] = $key . '="false"';
                }
            } else {
                $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $attributes[] = $key . '="' . $value . '"';
            }

        }
        $output =  '<' . $this->tag;
        if (count($attributes)) {
            $output .= ' ' . implode(' ', $attributes);
        }
        if ($this->content || !$this->selfClosing) {
            $output.= '>' . $this->content . '</' . $this->tag . '>';
        } else {
            $output.= ' />';
        }
        return $output;
    }

    public function __toString() {
        return $this->render();
    }
}
