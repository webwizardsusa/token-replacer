<?php

namespace Filapress\Media;

class MediaTypes
{
    protected array $types = [];

    public function __construct(array $types = [])
    {
        $this->register(...$types);
    }

    public function register(...$types): static
    {
        foreach ($types as $type) {
            if ($type instanceof MediaType) {
                $this->types[$type->name()] = $type;

                return $this;
            }
            if (is_array($type)) {
                if (! isset($type['class'])) {
                    throw new \InvalidArgumentException('Media type configuration must have a class');
                }
                $instance = app($type['class'], [
                    'configuration' => \Arr::except($type, ['class']),
                ]);
                $this->types[$instance->name()] = $instance;

                return $this;
            }

            $type = app($type);

            if (! $type instanceof MediaType) {
                throw new \InvalidArgumentException('Media types must extend MediaType');
            }

            $this->types[$type->name()] = $type;
        }

        return $this;
    }

    /**
     * @return array | MediaType[]
     */
    public function all(): array
    {
        return $this->types;
    }

    public function has(string $name): bool
    {
        return isset($this->types[$name]);
    }

    public function get(string $name): MediaType
    {
        if (! $this->has($name)) {
            throw new \InvalidArgumentException('Media type not found');
        }

        return $this->types[$name];
    }
}
