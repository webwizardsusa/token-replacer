<?php

namespace Filapress\Core\Events;

class FilapressPruneRegisterEvent
{
    protected array $models = [];

    public function register(...$models): static
    {
        foreach ($models as $model) {
            if (is_array($model)) {
                return $this->register(...$model);
            }
            $this->models[] = $model;
        }

        return $this;
    }

    public function get(): array
    {
        return $this->models;
    }
}
