<?php

namespace Filapress\RichEditor\Plugins;

use Closure;
use Filapress\RichEditor\FPRichEditor;
use Illuminate\Contracts\Support\Arrayable;

abstract class AbstractPlugin implements Arrayable
{
    protected ?Closure $afterStateHydrated = null;

    protected array $serverRequestListeners = [];

    public function setup(FPRichEditor $editor): void {}

    public function getHelp(): mixed
    {
        return null;
    }

    public function onServerRequest(string $method, Closure $callback): static
    {
        if (! isset($this->serverRequestListeners[$method])) {
            $this->serverRequestListeners[$method] = [];
        }
        $this->serverRequestListeners[$method][] = $callback;

        return $this;
    }

    public function getServerRequestListeners(): array
    {
        return $this->serverRequestListeners;
    }

    public function getServerRequestListener(string $method): array
    {
        return $this->serverRequestListeners[$method] ?? [];
    }

    abstract public function name(): string;

    public function getConfig(): array
    {
        return [];
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name(),
            'config' => $this->getConfig(),
        ];
    }

    public function stateHydratedCallback(): ?Closure
    {
        return $this->afterStateHydrated;
    }

    public function stateDehydrate($state): mixed
    {
        return $state;
    }
}
