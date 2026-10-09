<?php

namespace App\Business\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUserService
{
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['uuid'] = uuid_create(\UUID_TYPE_DEFAULT);
        
        $user = User::create($data)->load('roles');

        return $user;
    }
}