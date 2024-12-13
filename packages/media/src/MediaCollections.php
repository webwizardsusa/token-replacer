<?php

namespace Filapress\Media;

use Illuminate\Support\Collection;

class MediaCollections
{

    protected array $collections = [];

    public function __construct(array $collections = []) {
        foreach($collections as $collection) {
            $this->register($collection);
        }
    }

    public function register(string|MediaCollection $collection): static {
        if (is_string($collection)) {
            $collection = app($collection);
        }
        $this->collections[$collection->name()] = $collection;
        return $this;
    }

    public function get(string $name): MediaCollection {
        return $this->collections[$name];
    }

    public function has(string $name): bool {
        return isset($this->collections[$name]);
    }

    public function all(): Collection
    {
        return collect($this->collections);
    }
}
