<?php

namespace App\Business\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class UpdateUserService
{
    /**
     * Actualiza un usuario existente a partir de una clave primaria o UUID.
     *
     * @param string $identifier ID o UUID del usuario.
     * @param array $data Datos validados de la solicitud.
     * @return User
     */
    public function updateUser(string $identifier, array $data): User
    {
        // 1. Capa de seguridad defensiva: Eliminar 'uuid' si venía en el arreglo
        unset($data['uuid']);

        // 2. Mapear 'role_name' a 'role_id' si está presente en el payload
        if (isset($data['role_name'])) {
            $role = Role::where('name', $data['role_name'])->firstOrFail();
            $data['role_id'] = $role->id;
            unset($data['role_name']);
        }

        // 3. Procesar y hashear la contraseña si se envió una nueva
        if (!empty($data['new_password'])) {
            $data['password'] = Hash::make($data['new_password']);
            unset($data['new_password'], $data['old_password'], $data['confirm_password']);
        } elseif (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // 4. Buscar el registro por UUID o ID primario
        $user = User::where('uuid', $identifier)
            ->orWhere('id', $identifier)
            ->with('roles')
            ->firstOrFail();

        dd($user);


        $user->update($updateData);

        return $user->fresh();
    }
}