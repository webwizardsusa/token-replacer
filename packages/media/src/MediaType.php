<?php

namespace Filapress\Media;

use App\Models\User;
use Filament\Forms\Form;
use Filapress\Core\Traits\HasTraitHooks;
use Filapress\Media\Actions\RenderAction;
use Filapress\Media\Contracts\PathGeneratorContract;
use Filapress\Media\Images\ImageFactory;
use Filapress\Media\Models\FilapressMedia;
use Filapress\Media\Models\FilapressMediaVariant;
use Filapress\Media\PathGenerators\PathGeneratorFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class MediaType
{
    use HasTraitHooks;

    protected array $configuration;

    protected string $disk;


    public function __construct(array $configuration = [])
    {
        $this->configuration = $configuration;
        $this->disk = $this->config('disk', config('filapress.media.disk'));
        $this->setup();
    }



    protected function setup(): void {}

    public function config(string $keyName, mixed $default = null): mixed
    {
        return Arr::get($this->configuration, $keyName, $default);
    }

    abstract public function label(): string;

    public function name(): string
    {
        return Str::snake($this->label());
    }

    public function create(array $attributes = []): FilapressMedia
    {
        $instance = $this->make($attributes);
        $instance->save();
        return $instance;
    }
    public function make(array $attributes = []): FilapressMedia
    {
        $attributes['type'] = $this->name();
        if (!array_key_exists('user_id', $attributes)) {
            $attributes['user_id'] = auth()->id();
        }
        $file = Arr::get($attributes, 'path');
        $attributes = Arr::except($attributes, ['path']);
        $attributes['id'] = Str::orderedUuid();
        $media = FilapressMedia::make($attributes);
        if ($file instanceof UploadedFile) {
            $this->attachFile($media, $file);
        }

        return $media;
    }

    public function update(FilapressMedia $media, array $attributes = []): FilapressMedia
    {
        $file = Arr::get($attributes, 'path');
        $attributes = Arr::except($attributes, ['path']);
        $media->fill($attributes);
        if ($file instanceof UploadedFile) {
            $this->attachFile($media, $file);
        }

        return $media;
    }

    public function attachFile(FilapressMedia $media, string|UploadedFile $file): FilapressMedia {
        $media = $this->handleAttach($media, $file);
        $media->getCollection()?->afterAttach($media, $file);
        $this->callTraitHook('afterAttach', $media, $file);
        return $media;
    }

    abstract protected function handleAttach(FilapressMedia $media, string|UploadedFile $file): FilapressMedia;
    public function responsiveSizes(): array
    {
        return $this->config('responsiveSizes', config('filapress.media.responsive_sizes', []));
    }

    public function userCan(string $permission, ?User $user = null, ?FilapressMedia $media = null): bool
    {

        $method = 'can'.ucfirst($permission);
        $user = $user ?? auth()->user();

        if (method_exists($this, $method)) {
            if ($media && $media?->getCollection()) {
                $result = $media->getCollection()?->$method(user: $user, media: $media);
                if (is_bool($result)) {
                    return $result;
                }
            }

            return $this->$method($user, $media);
        }
        return false;
    }
    abstract public function canCreate(?User $user): bool;


    abstract public function canList(?User $user): bool;

    abstract public function canView(?User $user, FilapressMedia $media):bool;

    abstract public function canUpdate(?User $user, FilapressMedia $media):bool;

    abstract public function canDelete(?User $user, FilapressMedia $media):bool;

    abstract public function canForceDelete(?User $user, FilapressMedia $media):bool;

    abstract public function canRestore(?User $user, FilapressMedia $media):bool;

    abstract public function form(Form $form, ?FilapressMedia $record = null): array;

    public function __toString(): string
    {
        return $this->name();
    }

    public function thumbnailUrl(FilapressMedia $media): ?string
    {
        if ($media->thumbnail_disk && $media->thumbnail_path) {
            return Storage::disk($media->thumbnail_disk)->url($media->thumbnail_path);
        }
        return null;
    }


    public function renderAction(FilapressMedia $media): RenderAction
    {
        return RenderAction::make($media);
    }
    abstract public function render(FilapressMedia $media, ?FilapressMediaVariant $variant = null, array $attributes = [], bool $preview = false): mixed;

    abstract public function mediaInfo(FIlapressMedia $media): array;

    abstract public function insertForm(FIlapressMedia $media, Form $form): array;

    public function deleteFiles(FilapressMedia $media): FilapressMedia
    {
        if ($media->disk && $media->path) {
            $this->deleteFile($media->disk, $media->path);
            $media->sizes->each(fn($size) => $this->deleteFile($media->disk, $size['path']));
        }
        if ($media->thumbnail_disk && $media->thumbnail_path) {
            $this->deleteFile($media->thumbnail_disk, $media->thumbnail_path);
        }

        return $media;
    }

    protected function deleteFile(string $disk, string $path): void {
        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}
