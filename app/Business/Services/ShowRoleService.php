<?php

namespace App\Business\Services;

use App\Models\Role;

class ShowRoleService
{
    public function showRole(Role $role): Role
    {
        $role->load(['permissions']);

        return $role;
    }
}