<?php

namespace App\Policies;

use App\Models\User;

class RolePolicy
{
    public function store(User $user): bool
    {
        return $user->hasPermission('roles.create');
    }
}
