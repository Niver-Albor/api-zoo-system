<?php

namespace App\Business\Services;

use App\Business\DTOs\BaseDTO;
use App\Models\User;

class DeleteUserService
{
    public function deleteUser(User $user): bool
    {
        return $user->delete();
    }
}