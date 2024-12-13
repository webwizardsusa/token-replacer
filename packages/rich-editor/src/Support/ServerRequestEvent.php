<?php

namespace Filapress\RichEditor\Support;

use Illuminate\Support\Arr;

class ServerRequestEvent
{
    protected bool $stop = false;

    protected string $method;

    protected array $args;

    public function __construct(string $method, array $args)
    {

        $this->method = $method;
        $this->args = $args;
    }

    public function stop(): static
    {
        $this->stop = true;

        return $this;
    }

    public function shouldStop(): bool
    {
        return $this->stop;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function arg(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->args, $key, $default);
    }

    public function hasArg(string $key): bool
    {
        return Arr::has($this->args, $key);
    }
}
