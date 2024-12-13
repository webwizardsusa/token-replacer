<?php

namespace Filapress\Core\Auth;

use Filapress\Core\Events\RegisterPermissionsEvent;

class PermissionsRepository
{
    protected array $permissions = [];

    public function __construct()
    {
        event(new RegisterPermissionsEvent($this));
    }

    public function add(string $permission, string $description, ?string $group = null): static
    {
        $this->permissions[$permission] = [

            'permission' => $permission,
            'description' => $description,
            'group' => $group,
        ];

        return $this;
    }

    public function all(): array
    {
        return array_values($this->permissions);
    }
}
