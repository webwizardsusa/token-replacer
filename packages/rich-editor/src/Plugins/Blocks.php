<?php

namespace Filapress\RichEditor\Plugins;

class Blocks extends AbstractPlugin
{
    protected array $blocks = [];

    public static function make(): static
    {
        return new static;
    }

    public function name(): string
    {
        return 'blocks';
    }

    public function addBlock($label, $command, $attrs, $active): static
    {
        $ident = \Str::slug($label);
        $this->blocks[$ident] = [
            'label' => $label,
            'ident' => $ident,
            'command' => $command,
            'attrs' => $attrs,
            'active' => $active,
        ];

        return $this;
    }

    public function getConfig(): array
    {
        return ['blocks' => array_values($this->blocks)];
    }
}
