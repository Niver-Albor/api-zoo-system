<?php

namespace App\Business\Services;

use App\Business\DTOs\SaveUserData;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class UpdateUserService
{
    /**
     * Actualiza un usuario existente a partir de una clave primaria o UUID.
     *
     * @param User $user Target user to update.
     * @param SaveUserData $dto Datos validados de la solicitud.
     * @return User
     */
    public function updateUser(User $user, SaveUserData $dto): User
    {

        $data = $dto->toArray();

        if (isset($dto->role_name)) {
            $data['role_id'] = Role::where('name', $dto->role_name)->firstOrFail()->id;
        }

        if (isset($dto->password)) {
            $data['password'] = Hash::make($dto->password);
        }

        $user->update($data);

        return $user->fresh()->load('roles');
    }
}