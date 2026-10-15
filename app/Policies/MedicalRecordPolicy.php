<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;

class MedicalRecordPolicy
{
    /**
     * Determine wether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('medical.create');
    }
}
