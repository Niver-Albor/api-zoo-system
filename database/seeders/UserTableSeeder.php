<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRoleId = Role::where('name', 'admin')->value('id');
        $vetRoleId = Role::where('name', 'veterinarian')->value('id');

        User::factory()->create([
            'name'      => 'Admin',
            'email'     => 'admin@example.com',
            'password'  => Hash::make('qwerty'),
            'role_id'   => $adminRoleId
        ]);

        User::factory()->create([
            'name' => 'vet',
            'email' => 'vet@example.com',
            'role_id' => $vetRoleId
        ]);
    }
}
