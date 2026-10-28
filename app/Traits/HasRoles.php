<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Validation\ValidationException;

trait HasRoles
{
    public function validateUserPermissions(User $user, array $permissionsId)
    {
        if ($permissionsId)
            {
                foreach ($permissionsId as $per)
                    {
                        if (!$user->hasPermission($per))
                            throw ValidationException::withMessages([
                            'No se pueden asignar permisos que no se poseen'
                        ]);
                    }
            }
    }
}
