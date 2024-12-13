<?php

namespace Filapress\Core\Events;

use Filapress\Core\Auth\PermissionsRepository;

class RegisterPermissionsEvent
{
    public PermissionsRepository $permissions;

    public function __construct(PermissionsRepository $permissionsRepository)
    {
        $this->permissions = $permissionsRepository;
    }
}
