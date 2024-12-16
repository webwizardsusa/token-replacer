<?php

namespace Filapress\Core\Traits;

trait HasTraitHooks
{
    public function callTraitHook($hook, ...$args): void
    {
        foreach (class_uses_recursive($this) as $trait) {
            $method = lcfirst(class_basename($trait)).ucfirst($hook);
            if (method_exists($this, $method)) {
                $this->{$method}(...$args);
            }
        }
    }
}