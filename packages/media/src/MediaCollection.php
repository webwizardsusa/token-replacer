<?php

namespace Filapress\Media;

use App\Models\User;
use Filapress\Media\Models\FilapressMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

abstract class MediaCollection
{

    abstract public function label(): string;

    public function name(): string
    {
        return Str::snake($this->label());
    }

    public function canList(string $type, ?User $user = null): ?bool
    {
        return null;
    }

    public function canCreate(?User $user = null): ?bool
    {
        return null;
    }

    public function canView(User $user, FilapressMedia $media  ): ?bool
    {
        return null;
    }

    public function canUpdate(User $user, FilapressMedia $media): ?bool
    {
        return null;
    }

    public function canDelete(User $user, FilapressMedia $media): ?bool
    {
        return null;
    }

    public function canForceDelete(User $user, FilapressMedia $media): ?bool
    {
        return null;
    }

    public function canRestore(User $user, FilapressMedia $media): ?bool
    {
        return null;
    }

    public function afterAttach(FilapressMedia $media, string|UploadedFile $file): FilapressMedia
    {
        return $media;
    }

    public function variants(): array
    {
        return [];
    }
}
