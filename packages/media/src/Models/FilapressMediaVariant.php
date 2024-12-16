<?php

namespace Filapress\Media\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Storage;

/**
 * @property Collection $sizes;
 */
class FilapressMediaVariant extends Model
{
    use HasUuids;

    protected $table = 'filapress_media_variants';

    protected $guarded = [];

    protected $casts = [
        'sizes' => 'array',
        'width' => 'integer',
        'height' => 'integer',
        'filesize' => 'integer',
    ];

    public static function boot(): void
    {
        parent::boot();
        static::deleted(function (FilapressMediaVariant $variant) {
            $variant->deleteFiles();
        });
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(FilapressMedia::class, 'media_id');
    }

    public function getSizesAttribute($value): Collection
    {
        $value = is_array($value) ? $value : (array) json_decode($value, true) ?? [];

        return collect($value);
    }

    public function deleteSize(string $name): static
    {
        $sizes = $this->sizes;
        if ($sizes->has($name) && Storage::disk($this->disk)->exists($sizes[$name]['path'])) {
            Storage::disk($this->disk)->delete($sizes[$name]['path']);
            $sizes->forget($name);
        }
        $this->sizes = $sizes->toArray();

        return $this;
    }

    public function addSize(string $name, array $data): static
    {
        $this->deleteSize($name);
        $sizes = $this->sizes;
        $sizes[$name] = $data;
        $this->sizes = $sizes->toArray();

        return $this;
    }

    public function getUrl(?string $name = null): ?string
    {
        if (! $name) {
            return Storage::disk($this->disk)->url($this->path);
        }
        $sizes = $this->sizes;
        if ($sizes->has($name)) {
            return Storage::disk($this->disk)->url($sizes[$name]['path']);
        }

        return null;
    }

    public function render(?string $size = null, array $attributes = [], bool $preview = false)
    {
        return $this->getType()->render($this, $size, $attributes, $preview);
    }

    public function deleteFiles(): static
    {
        if ($this->disk && $this->path) {
            Storage::disk($this->disk)->delete($this->path);
            $this->sizes->each(function ($size) {
                Storage::disk($this->disk)->delete($size['path']);
            });
        }
        $this->disk = '';
        $this->path = '';
        $this->sizes = [];

        return $this;
    }
}
