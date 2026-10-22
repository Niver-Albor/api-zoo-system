<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionAndRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Módulo de Animales
            ['name' => 'animals.view', 'description' => 'Ver listado y detalle de animales'],
            ['name' => 'animals.create', 'description' => 'Registrar nuevos animales'],
            ['name' => 'animals.update', 'description' => 'Editar datos de animales'],
            ['name' => 'animals.delete', 'description' => 'Eliminar o dar de baja animales'],

            // Módulo Médico
            ['name' => 'medical.view', 'description' => 'Ver historial médico'],
            ['name' => 'medical.create', 'description' => 'Crear consultas o fichas médicas'],
            ['name' => 'medical.update_own', 'description' => 'Actualiza sus fichas médicas'],
            ['name' => 'medical.update_any', 'description' => 'Actualiza cualquier ficha médica'],

            // Módulo de Hábitats
            ['name' => 'habitats.manage', 'description' => 'Administrar hábitats y capacidades'],

            // Módulo de Usuarios
            ['name' => 'users.create', 'description' => 'Registrar nuevos usuarios'],
            ['name' => 'users.view_any', 'description' => 'Ver listado general de los usuarios'],
            ['name' => 'users.update_any', 'description' => 'Editar datos de cualquier usuario'],
            ['name' => 'users.update_own', 'description' => 'Editar los registros de solo su usuario'],
            ['name' => 'users.delete_any', 'description' => 'Eliminar o dar de baja a cualquier usuario'],

            // Módulo de Roles
            ['name' => 'roles.assign', 'description' => 'Asignar roles'],

            // Módulo de Especies
            ['name' => 'species.create', 'description'  => 'Registrar nuevas especies'],
            ['name' => 'species.update', 'description'  => 'Editar datos de cualquier especie'],
            ['name' => 'species.view', 'description'    => 'Ver listado general de las especies'],
            ['name' => 'species.delete', 'description'  => 'Eliminar o dar de baja cualquier especie'],
        ];

        foreach ($permissions as $permission)
            {
                Permission::firstOrCreate(['name' => $permission['name']], $permission);
            }

        $adminRole = Role::firstOrCreate(['name' => 'owner'], ['description' => 'Administrador general']);
        $vetRole = Role::firstOrCreate(['name' => 'veterinarian'], ['description' => 'Personal médico']);
        $assistantRole = Role::firstOrCreate(['name' => 'assistant'], ['description' => 'Auxiliar técnico']);

        $allPermissionIds = Permission::pluck('id');
        $adminRole->permissions()->sync($allPermissionIds);

        $vetPermissionIds = Permission::whereIn('name', [
            'animals.view',
            'medical.view',
            'medical.create',
            'medical.update_own'
        ])->pluck('id');
        $vetRole->permissions()->sync($vetPermissionIds);

        $assistantPermissionIds = Permission::whereIn('name', [
            'medical.view',
            'users.update_own',
            'animals.view',
        ])->pluck('id');
        $assistantRole->permissions()->sync($assistantPermissionIds);
    }
}
