<?php

namespace App\Policies;

use App\Models\User;

class AnimalPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function store(User $user): bool
    {
        return $user->hasPermission('animals.create');
    }

    public function update(User $user): bool
    {
        return $user->hasPermission('animals.update');
    }

    public function index(User $user): bool
    {
        return $user->hasPermission('animals.view');
    }

    public function show(User $user): bool
    {
        return $user->hasPermission('animals.view');
    }

    public function destroy(User $user): bool
    {
        return $user->hasPermission('animals.delete');
    }
}
