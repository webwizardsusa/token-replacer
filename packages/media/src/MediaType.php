<?php

namespace Filapress\Media;

use App\Models\User;
use Filament\Forms\Form;
use Filapress\Core\Traits\HasTraitHooks;
use Filapress\Media\Actions\RenderAction;
use Filapress\Media\Models\FilapressMedia;
use Filapress\Media\Models\FilapressMediaVariant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Represents an abstract Media Type within the application.
 */
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

    /**
     * Create and save a new media instance with the provided attributes.
     *
     * @param array $attributes Attributes to initialize the media instance.
     *
     * @return FilapressMedia The newly created and saved media instance.
     */
    public function create(array $attributes = []): FilapressMedia
    {
        $instance = $this->make($attributes);
        $instance->save();

        return $instance;
    }

    /**
     * Create a new FilapressMedia instance with the specified attributes.
     *
     * @param array $attributes The attributes used to create the media instance.
     *                           Must include 'path' if a file is being attached.
     *
     * @return FilapressMedia The created media instance.
     */
    public function make(array $attributes = []): FilapressMedia
    {
        $attributes['type'] = $this->name();
        if (! array_key_exists('user_id', $attributes)) {
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

    /**
     * Update the specified media instance with given attributes and optionally attach a file.
     *
     * @param FilapressMedia $media The media instance to update.
     * @param array $attributes The attributes to update, including an optional file path.
     *
     * @return FilapressMedia The updated media instance.
     */
    public function update(FilapressMedia $media, array $attributes = []): FilapressMedia
    {
        $file = Arr::get($attributes, 'path');
        $attributes = Arr::except($attributes, ['path']);
        $media->fill($attributes);
        if ($file instanceof UploadedFile) {
            $this->attachFile($media, $file);
        }

        $media->save();
        return $media;
    }

    /**
     * Attach a file to the given media instance.
     *
     * @param FilapressMedia $media The media instance to which the file will be attached.
     * @param string|UploadedFile $file The file to attach, provided as a file path or UploadedFile instance.
     *
     * @return FilapressMedia The updated media instance after the file is attached.
     */
    public function attachFile(FilapressMedia $media, string|UploadedFile $file): FilapressMedia
    {
        $media = $this->handleAttach($media, $file);
        $media->getCollection()?->afterAttach($media, $file);
        $this->callTraitHook('afterAttach', $media, $file);

        return $media;
    }

    /**
     * Handle the attachment process for a given media instance and file.
     *
     * @param FilapressMedia $media The media instance to attach the file to.
     * @param string|UploadedFile $file The file to be attached, provided as a path or an uploaded file instance.
     *
     * @return FilapressMedia The updated media instance after attachment.
     */
    abstract protected function handleAttach(FilapressMedia $media, string|UploadedFile $file): FilapressMedia;

    public function responsiveSizes(): array
    {
        return $this->config('responsiveSizes', config('filapress.media.responsive_sizes', []));
    }

    /**
     * Determine if the specified user has a given permission, optionally scoped to a media instance.
     *
     * @param string $permission The name of the permission to check.
     * @param User|null $user The user to check the permission for. Defaults to the authenticated user if null.
     * @param FilapressMedia|null $media An optional media instance to scope the permission check.
     *
     * @return bool True if the user has the specified permission; false otherwise.
     */
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

    /**
     * Determine if the given user has permission to create a resource.
     *
     * @param User|null $user The user for whom the permission check is being performed.
     * @return bool True if the user has the permission to create, false otherwise.
     */
    abstract public function canCreate(?User $user): bool;

    /**
     * Determine if the given user has permission to list resources.
     *
     * @param User|null $user The user for whom the permission check is being performed.
     * @return bool True if the user has the permission to list, false otherwise.
     */
    abstract public function canList(?User $user): bool;


    /**
     * Determine if the given user has permission to view the specified media resource.
     *
     * @param User|null $user The user for whom the permission check is being performed.
     * @param FilapressMedia $media The media resource to check access for.
     * @return bool True if the user has the permission to view, false otherwise.
     */
    abstract public function canView(?User $user, FilapressMedia $media): bool;

    /**
     * Determine if the given user has permission to update the specified media resource.
     *
     * @param User|null $user The user for whom the permission check is being performed.
     * @param FilapressMedia $media The media resource that the user intends to update.
     * @return bool True if the user has the permission to update the media, false otherwise.
     */
    abstract public function canUpdate(?User $user, FilapressMedia $media): bool;

    /**
     * Determine if the given user has permission to delete the specified media resource.
     *
     * @param User|null $user The user for whom the permission check is being performed.
     * @param FilapressMedia $media The media resource to be deleted.
     * @return bool True if the user has the permission to delete the media, false otherwise.
     */
    abstract public function canDelete(?User $user, FilapressMedia $media): bool;

    /**
     * Determine if the given user has permission to permanently delete the specified media resource.
     *
     * @param User|null $user The user for whom the permission check is being performed.
     * @param FilapressMedia $media The media resource that is being checked for permanent deletion permission.
     * @return bool True if the user has the permission to force delete the media, false otherwise.
     */
    abstract public function canForceDelete(?User $user, FilapressMedia $media): bool;

    /**
     * Determine if the given user has permission to restore the specified media resource.
     *
     * @param User|null $user The user for whom the permission check is being performed.
     * @param FilapressMedia $media The media resource to be restored.
     * @return bool True if the user has the permission to restore, false otherwise.
     */
    abstract public function canRestore(?User $user, FilapressMedia $media): bool;

    /**
     * Generate the form structure for the given resource.
     *
     * @param Form $form The form instance to be configured.
     * @param FilapressMedia|null $record The existing resource record, if available, for editing.
     * @return array An array representation of the form structure.
     */
    abstract public function form(Form $form, ?FilapressMedia $record = null): array;

    public function __toString(): string
    {
        return $this->name();
    }

    /**
     * Retrieve the URL of the thumbnail for the given media item.
     *
     * @param FilapressMedia $media The media item for which the thumbnail URL is being retrieved.
     * @return string|null The thumbnail URL if available, or null if no thumbnail exists.
     */
    public function thumbnailUrl(FilapressMedia $media): ?string
    {
        if ($media->thumbnail_disk && $media->thumbnail_path) {
            return Storage::disk($media->thumbnail_disk)->url($media->thumbnail_path);
        }

        return null;
    }

    /**
     * Render an action for the given media resource.
     *
     * @param FilapressMedia $media The media resource for which the render action is being created.
     * @return RenderAction The rendering action for the specified media resource.
     */
    public function renderAction(FilapressMedia $media): RenderAction
    {
        return RenderAction::make($media);
    }

    /**
     * Render the specified media with optional variant, attributes, and preview mode.
     *
     * @param FilapressMedia $media The media instance to render.
     * @param FilapressMediaVariant|null $variant An optional media variant to render.
     * @param array $attributes Additional attributes for rendering.
     * @param bool $preview Whether to render in preview mode.
     *
     * @return mixed The rendered output.
     */
    abstract public function render(FilapressMedia $media, ?FilapressMediaVariant $variant = null, array $attributes = [], bool $preview = false): mixed;

    /**
     * Retrieve information about the specified media.
     *
     * @param FilapressMedia $media The media instance to retrieve information for.
     *
     * @return array An array containing media information.
     */
    abstract public function mediaInfo(FIlapressMedia $media): array;

    /**
     * Insert the specified media into the given form.
     *
     * @param FIlapressMedia $media The media instance to be inserted.
     * @param Form $form The form instance where the media is to be inserted.
     *
     * @return array An array representing the result of the insertion operation.
     */
    abstract public function insertForm(FIlapressMedia $media, Form $form): array;

    /**
     * Delete all associated files of the specified media, including sizes and thumbnails.
     *
     * @param FilapressMedia $media The media instance whose files will be deleted.
     *
     * @return FilapressMedia The media instance after the files have been deleted.
     */
    public function deleteFiles(FilapressMedia $media): FilapressMedia
    {
        if ($media->disk && $media->path) {
            $this->deleteFile($media->disk, $media->path);
            $media->sizes->each(fn ($size) => $this->deleteFile($media->disk, $size['path']));
        }
        if ($media->thumbnail_disk && $media->thumbnail_path) {
            $this->deleteFile($media->thumbnail_disk, $media->thumbnail_path);
        }

        return $media;
    }

    protected function deleteFile(string $disk, string $path): void
    {
        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}
