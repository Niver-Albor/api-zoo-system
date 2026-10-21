<?php

namespace App\Policies;

use App\Models\Species;
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

    public function create(User $user): bool
    {
        return $user->hasPermission('species.create');
    }

    public function update(User $user, Species $species): bool
    {
        return $user->hasPermission('species.update');
    }

    public function view(User $user): bool
    {
        return $user->hasPermission('species.view');
    }
}
