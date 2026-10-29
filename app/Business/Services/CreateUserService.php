<?php

namespace App\Business\Services;

use App\Business\DTOs\SaveUserData;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class CreateUserService
{
    public function createUser(SaveUserData $dto): User
    {
        $data = $dto->toArray();
        $data['role_id'] = Role::where('name', $dto->role_name)->firstOrFail()->id;
        $data['password'] = Hash::make($data['password']);
        $data['uuid'] = uuid_create(\UUID_TYPE_DEFAULT);
        
        $user = User::create($data)->load('role');

        return $user;
    }
}