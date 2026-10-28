<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function store(User $user): bool
    {
        return $user->hasPermission('roles.create');
    }

    public function update(User $user, Role $role): bool
    {
        if ($role->is_system)
            return false;

        if ($user->role->id === $role->id)
            return false;

        return $user->hasPermission('roles.update');
    }
}
