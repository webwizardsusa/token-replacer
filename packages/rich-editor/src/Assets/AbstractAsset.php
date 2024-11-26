<?php

namespace Filapress\RichEditor\Assets;

use Illuminate\Contracts\Support\Arrayable;

abstract class AbstractAsset implements Arrayable
{
    protected string $src;

    public function __construct($src)
    {
        $this->src = $src;
    }

    public static function make($src): static
    {
        return new static($src);
    }
    public function getSrc(): string
    {
        return $this->src;
    }
}
