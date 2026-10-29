<?php

namespace App\Business\Services;

use App\Models\Role;
use DomainException;

class DeleteRoleService
{
    public function deleteRole(Role $role): bool
    {
        if ($role->users()->exists())
            {
                throw new DomainException('No se puede eliminar el rol porque aún tiene usuarios asociados.');
            }

        return $role->delete();
    }
}