<?php

namespace Filapress\Media\Filament\Components\Form;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\HasName;

class MediaInfoField extends Component
{
    use HasName;

    protected array|Closure $data = [];

    protected string $view = 'filapress-media::components.form.media-info';

    final public function __construct(string $name, array|Closure $data = [])
    {
        $this->name($name);
        $this->data = $data;
        $this->statePath($name);
    }

    public static function make(string $name, array|Closure $data = []): static
    {
        return new static($name, $data);
    }

    public function data(array|Closure $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): array
    {
        return $this->evaluate($this->data);
    }
}
