<?php

namespace Webwizardsusa\HtmlRefiner;

use Webwizardsusa\HtmlRefiner\Filters\AbstractFilter;

abstract class RefinerDefinition
{

    protected string $input;

    protected ?string $context = null;

    protected array $customElements = [];

    public function __construct(string $input)
    {
        $this->input = $input;
        $this->setup();
    }

    public function setup() {

    }

    public static function for(string $html): static
    {
        return new static($html);
    }

    public function context(?string $context): static
    {
        $this->context = $context;
        return $this;
    }

    public function getInput(): string
    {
        return $this->input;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function customElement(CustomElement $element): static
    {
        $this->customElements[$element->tag()] = $element;
        return $this;
    }

    /**
     * @return array | CustomElement[]
     */
    public function getCustomElements(): array
    {
        return $this->customElements;
    }


    /**
     * @return array | AbstractFilter[]
     */
    abstract public function filters(): array;

    public function parse(): string
    {
        $html = $this->input;
        foreach($this->getCustomElements() as $element) {
            $html = $element->preFilter($html, $this);
        }

        foreach($this->filters() as $filter) {
            $html = $filter->preProcess($html, $this);
        }

        foreach($this->filters() as $filter) {
            $html = $filter->process($html, $this);
        }

        foreach($this->filters() as $filter) {
            $html = $filter->postProcess($html, $this);
        }
        foreach($this->getCustomElements() as $element) {
            $html = $element->render($html, $this);
        }

        return $html;
    }

    public function __toString(): string
    {
        return $this->parse();
    }
}
