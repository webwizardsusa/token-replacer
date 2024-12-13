<?php

namespace Filapress\Media\Policies;

use App\Models\User;
use Filapress\Media\MediaTypes;
use Filapress\Media\Models\FilapressMedia;
use Illuminate\Auth\Access\HandlesAuthorization;

class FilapressMediaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user, ?string $type = null): bool
    {
        if ($type) {
            return app(MediaTypes::class)->get($type)?->userCan('list', $user);
        }
        foreach (app(MediaTypes::class)->all() as $mediaType) {
            if ($mediaType->userCan('list', $user)) {
                return true;
            }
        }

        return false;
    }

    protected function checkPermission(string $permission, ?User $user,FilapressMedia $media): bool{
        return $media->getType()->userCan($permission, $user, $media);
    }
    public function view(User $user, FilapressMedia $filapressMedia): bool
    {
        return $this->checkPermission('view', $user, $filapressMedia);
    }

    public function create(User $user, ?string $type = null): bool
    {
        if ($type) {
            return app(MediaTypes::class)->get($type)?->userCan('create', $user);
        }

        return false;
    }

    public function update(User $user, FilapressMedia $filapressMedia): bool
    {
        return $filapressMedia->getType()->userCan('update', $user, $filapressMedia);
    }

    public function delete(User $user, FilapressMedia $filapressMedia): bool
    {
        return $filapressMedia->getType()->userCan('delete', $user, $filapressMedia);
    }

    public function restore(User $user, FilapressMedia $filapressMedia): bool
    {
        return $filapressMedia->getType()->userCan('restore', $user, $filapressMedia);
    }

    public function forceDelete(User $user, FilapressMedia $filapressMedia): bool
    {
        return $filapressMedia->getType()->userCan('forceDelete', $user, $filapressMedia);
    }
}
