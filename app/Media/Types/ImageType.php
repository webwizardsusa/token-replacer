<?php

namespace App\Media\Types;

use App\Models\User;
use Filapress\Media\Models\FilapressMedia;

class ImageType extends \Filapress\Media\Types\ImageType
{
    public function canCreate(?User $user): bool
    {
        return $user !== null;
    }

    public function canList(?User $user): bool
    {
        return $user !== null;
    }

    public function canView(?User $user, FilapressMedia $media): bool
    {
        return $user !== null;
    }

    public function canUpdate(?User $user, FilapressMedia $media): bool
    {
        return $user !== null;
    }

    public function canDelete(?User $user, FilapressMedia $media): bool
    {
        return $user !== null;
    }

    public function canForceDelete(?User $user, FilapressMedia $media): bool
    {
        return $user !== null;
    }

    public function canRestore(?User $user, FilapressMedia $media): bool
    {
        return $user !== null;
    }
}
