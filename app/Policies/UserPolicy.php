<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $authUser, User $targetUser): bool
    {
        if ($authUser->hasPermission('users.view_any') || $authUser->id === $targetUser->id)
            return true;

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('users.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $authUser, User $targetUser): bool
    {
        // Si se puede editar a si mismo, o a otros
        $canEditProfile = $authUser->id === $targetUser->id || $authUser->hasPermission('users.update_any');

        if (! $canEditProfile)
            return false;

        if (request()->has('role_name'))
            {
                $requestedRole = request()->input('role_name');
                // No puede cambiar su propio rol
                if ($authUser->id === $targetUser->id) return false;

                if (! $authUser->hasPermission('roles.assign')) return false;
                
                // No se pueden asignar esos roles
                $protectedRole = ['owner', 'super-admin'];
                if (in_array($requestedRole, $protectedRole, true) && ! $authUser->hasRole('super-admin'))
                    return false;
            }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $authUser, User $targetUser): bool
    {
        if (! $authUser->hasPermission('users.delete_any'))
            return false;
        // No puede desactivar su propia cuenta.
        if ($authUser->id === $targetUser->id)
            return false;

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
