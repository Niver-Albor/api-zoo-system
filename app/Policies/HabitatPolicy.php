<?php

namespace App\Policies;

use App\Models\User;

class HabitatPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function store(User $user)
    {
        return $user->hasPermission('habitats.create');
    }
}
