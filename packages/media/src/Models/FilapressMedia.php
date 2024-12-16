<?php

namespace Filapress\Media\Models;

use App\Models\User;
use Filapress\Media\Actions\RenderAction;
use Filapress\Media\Dev\FilapressMediaFactory;
use Filapress\Media\MediaCollection;
use Filapress\Media\MediaCollections;
use Filapress\Media\MediaType;
use Filapress\Media\MediaTypes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Storage;

/**
 * @property Collection $sizes;
 */
class FilapressMedia extends Model
{
    use HasFactory, HasUuids, Prunable, SoftDeletes;

    protected $table = 'filapress_media';

    protected static string $factory = FilapressMediaFactory::class;

    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
        'sizes' => 'array',
        'status' => 'boolean',
        'width' => 'integer',
        'height' => 'integer',
        'filesize' => 'integer',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::forceDeleting(function (FilapressMedia $media) {
            $media->variants->each(fn (FilapressMediaVariant $variant) => $variant->delete());
            $media->getType()->deleteFiles($media);
        });

    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(FilapressMediaVariant::class, 'media_id');
    }

    public function getType(): MediaType
    {
        return app(MediaTypes::class)->get($this->type);
    }

    public function attachFile(string|UploadedFile $file): static
    {
        $this->getType()->attachFile($this, $file);

        return $this;
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

    /**
     * Renders the actual media item.
     *
     * @param  string|null  $variant  The name of the variant to render, or null for the original.
     * @param  array  $attributes  Attributes to apply to the rendered output.
     * @param  bool  $preview  True is this is an admin preview.
     */
    public function render(?string $variant = null, array $attributes = [], bool $preview = false): RenderAction
    {
        return $this->getType()->renderAction($this)

            ->variant($variant)
            ->attributes($attributes)
            ->preview($preview);
    }

    public function thumbnail(): ?string
    {
        return $this->getType()->thumbnailUrl($this);
    }

    public function getExtra(string $key): mixed
    {
        $extra = $this->data;
        if (is_array($extra) && array_key_exists($key, $extra)) {
            return $extra[$key];
        }

        return null;
    }

    public function getCollection(): ?MediaCollection
    {
        $mediaCollections = app(MediaCollections::class);
        if ($this->collection && $mediaCollections->has($this->collection)) {
            return $mediaCollections->get($this->collection);
        }

        return null;
    }

    public function usages(): HasMany
    {
        return $this->hasMany(FilapressMediaUsage::class, 'media_id');
    }

    public function prunable(): Builder
    {
        $interval = \DateInterval::createFromDateString(config('filapress.media.prune_after'));

        return static::where('deleted_at', '<=', now()->sub($interval));
    }
}
