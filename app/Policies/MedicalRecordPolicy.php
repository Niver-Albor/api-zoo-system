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

    /**
     * Determine wether the user can update de model.
     */
    public function update(User $user, MedicalRecord $record): bool
    {
        if ($user->hasPermission('medical.update_any'))
            return true;
        
        if ($user->hasPermission('medical.update_own') && $record->user_id === $user->id)
            return true;
        
        return false;
    }
}
