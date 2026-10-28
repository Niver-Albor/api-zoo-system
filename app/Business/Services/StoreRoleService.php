<?php

namespace App\Business\Services;

use App\Business\DTOs\SaveRoleData;
use App\Models\Role;
use App\Models\User;
use App\Traits\HasRoles;

class StoreRoleService
{
    use HasRoles;

    public function storeRole(SaveRoleData $dto, User $user): Role
    {
        
        $this->validateUserPermissions($user, $dto->permissionsId);

        $role = Role::create([
            'name'          => $dto->name,
            'description'   => $dto->description
        ]);

        if ($dto->permissionsId)
            $role->permissions()->sync($dto->permissionsId);

        $role->load(['permissions']);

        return $role;
    }
}