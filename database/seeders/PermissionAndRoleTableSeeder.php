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

            // Módulo de Hábitats
            ['name' => 'habitats.manage', 'description' => 'Administrar hábitats y capacidades'],
        ];

        foreach ($permissions as $permission)
            {
                Permission::firstOrCreate(['name' => $permission['name']], $permission);
            }

        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['description' => 'Administrador general']);
        $vetRole = Role::firstOrCreate(['name' => 'veterinarian'], ['description' => 'Personal médico']);

        $allPermissionIds = Permission::pluck('id');
        $adminRole->permissions()->sync($allPermissionIds);

        $vetPermissionIds = Permission::whereIn('name', [
            'animals.view',
            'medical.view',
            'medical.create'
        ])->pluck('id');
        $vetRole->permissions()->sync($vetPermissionIds);
    }
}
