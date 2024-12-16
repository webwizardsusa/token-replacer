<?php

namespace Filapress\Core\Assets;

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filapress\Core\Filapress;

class FilapressJs extends Js
{
    /** @var array | FilapressJs[] */
    protected static array $registered = [];

    protected string $devPath;

    protected bool $isHot = false;

    public static function make(string $id, ?string $path = null): static
    {
        $instance = app(static::class, ['id' => $id, 'path' => $path]);
        static::$registered[] = $instance;

        return $instance;
    }

    public static function getInstance(string $id, string $package): ?static
    {
        FilamentAsset::getScripts();

        foreach (static::$registered as $instance) {
            if (isset($instance->package) && $id === $instance->id && $package === $instance->package) {
                return $instance;
            }
        }

        return null;
    }

    public function dev(string $devPath): static
    {
        $this->devPath = $devPath;

        return $this;
    }

    public function getSrc(): string
    {
        if (! $this->isHot()) {
            return parent::getSrc();
        }
        $path = trim(str_replace(base_path(), '', realpath($this->devPath)), '/');
        if (! str_starts_with($path, 'packages/')) {
            return parent::getSrc();
        }
        $this->isHot = true;

        return \Vite::asset($path);
    }

    public function isHot(): bool
    {
        return $this->devPath && Filapress::viteRunningHot();
    }

    public function fileIsHot(): bool
    {
        return $this->isHot();

    }

    public function render(): string
    {
        $output = '<script src="'.$this->getSrc().'"';
        if ($this->isHot()) {
            $output .= ' type="module"';
        }

        return $output.'></script>';
    }
}
