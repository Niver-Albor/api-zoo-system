<?php

namespace App\Business\Services;

use App\Business\DTOs\SaveRoleData;
use App\Models\Role;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class StoreRoleService
{
    public function storeRole(SaveRoleData $dto, User $user): Role
    {
        if ($dto->permissionsId)
            {
                foreach ($dto->permissionsId as $per)
                    {
                        if (!$user->hasPermission($per))
                            throw ValidationException::withMessages([
                            'No se pueden asignar permisos que no se poseen'
                        ]);
                    }
            }


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