<?php

namespace App\Business\Services;

use App\Models\User;
use App\Business\Entities\UserEntity;
use Illuminate\Support\Facades\Hash;

class CreateUserService
{
    public function createUser(UserEntity $entity)
    {
        $entity->password = Hash::make($entity->password);
        $entity->uuid = uuid_create(\UUID_TYPE_DEFAULT);
        
        $user = User::create($entity->toArray());

        $entity->id = $user->id;

        return $entity->toArray();
    }
}