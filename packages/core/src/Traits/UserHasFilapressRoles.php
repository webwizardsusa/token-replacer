<?php

namespace Filapress\Core\Traits;

use Filapress\Core\Model\FilapressRole;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * @property FilapressRole[] | Collection $filapressRoles
 * @property array $filapressPermissions;
 *
 * @mixin Model
 */
trait UserHasFilapressRoles
{
    public function filapressRoles(): BelongsToMany
    {
        return $this->belongsToMany(FilapressRole::class, 'filapress_user_roles');
    }

    public function hasFilapressPermission(string $permission): bool
    {
        return in_array($permission, $this->filapressPermissions) || in_array('full_access', $this->filapressPermissions);
    }

    public function filapressPermissions(): Attribute
    {
        return Attribute::make(
            get: function () {
                $permissions = [];
                foreach ($this->filapressRoles as $role) {
                    $permissions = array_merge($permissions, $role->permissions);
                }

                return array_unique($permissions);
            },
        )->shouldCache();
    }
}
