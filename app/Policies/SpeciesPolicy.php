<?php

namespace App\Policies;

use App\Models\User;

class SpeciesPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function create(User $user)
    {
        return $user->hasPermission('species.create');
    }
}
