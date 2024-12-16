<?php

namespace Filapress\Media;

use Exception;

class ImageVariants
{
    /**
     * @var array | ImageVariant[]
     */
    protected array $variants;

    public function __construct(array $variants = [])
    {
        if (! empty($variants)) {
            $this->register(...$variants);

        }

    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(...$variants): static
    {
        foreach ($variants as $variant) {
            if (is_array($variant)) {
                return $this->register(...$variant);
            }
            if (is_string($variant)) {
                $variant = app($variant);
            }
            if (! $variant instanceof ImageVariant) {
                throw new Exception('Invalid variant');
            }

            $this->variants[$variant->name()] = $variant;
        }

        return $this;
    }

    public function get(string $name): ImageVariant
    {
        return $this->variants[$name];
    }

    public function has(string $name): bool
    {
        return isset($this->variants[$name]);
    }
}
