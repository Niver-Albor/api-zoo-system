<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    $this->role = Role::factory()->create();
    $this->user = User::factory()->create(['role_id' => $this->role->id]);

    $this->actingAs($this->user, 'sanctum');
});

uses(RefreshDatabase::class);

// Store
test('Registra correctamente un rol', function () {
    $permission = Permission::factory()->create(['name' => 'roles.create']);
    Permission::factory(5)->create();
    $allPermissionIds = Permission::all()->pluck('id');
    $this->role->permissions()->sync($allPermissionIds);

    $permissionsResponse = $this->getJson('/api/permissions');

    // Devuelve los permisos correctamente.
    $permissionsResponse->assertStatus(Response::HTTP_OK);

    $permissionsToAssign = collect($permissionsResponse->json('data')->take(2)->pluck('id')->toArray());

    $payload = [
        'name'              => '      Rol nuevo del TEST    ',
        'description'       => 'descripcion del rol',
        'permissions_id'    => $permissionsToAssign
    ];

    $roleResponse = $this->postJson('/api/roles', $payload);


    $roleResponse->assertStatus(Response::HTTP_CREATED)
        ->assertJsonPath('data.name', strtoupper(trim($payload['name'])))
        ->assertJsonCount(2, 'data.permissions');
});

test('Falla al tratar de crear un rol con datos invalidos', function () {
    $permission = Permission::factory()->create(['name' => 'roles.create']);
    $this->role->permissions()->sync($permission->id);

    $oldRole = Role::factory()->create();

    $permissionsResponse = $this->getJson('/api/permissions');

    $payload = [
        'name'              => $oldRole->name,
        'description'       => 1234567,
        'permissions_id'    => [7,3,99],
    ];

    $roleResponse = $this->postJson('/api/roles', $payload);


    $roleResponse->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonStructure([
            'message'   => [
                'name',
                'description',
                'permissions_id'
            ],
            'success'
        ]);
});

test('Falla al tratar de creat un rol sin permisos (roles.update o roles.create)', function () {
    Permission::factory(5)->create();
    $allPermissionIds = Permission::all()->pluck('id');
    $this->role->permissions()->sync($allPermissionIds); // Necesita tener todos los permisos si pretende asignarlos a un rol.

    $permissionsResponse = $this->getJson('/api/permissions');

    // Devuelve los permisos correctamente.
    $permissionsResponse->assertStatus(Response::HTTP_OK);

    $permissionsToAssign = collect($permissionsResponse->json('data')->take(2)->pluck('id')->toArray());

    $payload = [
        'name'              => '      Rol nuevo del TEST    ',
        'description'       => 'descripcion del rol',
        'permissions_id'    => $permissionsToAssign
    ];

    $roleResponse = $this->postJson('/api/roles', $payload);

    $roleResponse->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de crear un rol sin autenticación', function () {
    $this->app['auth']->forgetGuards();
    $permission = Permission::factory()->create(['name' => 'roles.create']);
    Permission::factory(5)->create();
    $allPermissionIds = Permission::all()->pluck('id');
    $this->role->permissions()->sync($allPermissionIds); // Necesita tener todos los permisos si pretende asignarlos a un rol.

    $permissionsResponse = $this->getJson('/api/permissions');

    // Devuelve los permisos correctamente.
    $permissionsResponse->assertStatus(Response::HTTP_OK);

    $permissionsToAssign = collect($permissionsResponse->json('data')->take(2)->pluck('id')->toArray());

    $payload = [
        'name'              => '      Rol nuevo del TEST    ',
        'description'       => 'descripcion del rol',
        'permissions_id'    => $permissionsToAssign
    ];

    $roleResponse = $this->postJson('/api/roles', $payload);

    $roleResponse->assertStatus(Response::HTTP_UNAUTHORIZED)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de crear un rol sin suficientes permisos', function () {
    $permission = Permission::factory()->create(['name' => 'roles.create']);
    $this->role->permissions()->sync($permission->id);

    Permission::factory(5)->create();

    $permissionsResponse = $this->getJson('/api/permissions');

    // Devuelve los permisos correctamente.
    $permissionsResponse->assertStatus(Response::HTTP_OK);

    $permissionsToAssign = collect($permissionsResponse->json('data')->take(2)->pluck('id')->toArray());

    $payload = [
        'name'              => '      Rol nuevo del TEST    ',
        'description'       => 'descripcion del rol',
        'permissions_id'    => $permissionsToAssign
    ];

    $roleResponse = $this->postJson('/api/roles', $payload);

    $roleResponse->assertStatus(Response::HTTP_BAD_REQUEST)
        ->assertJsonPath('success', false);
});

// Update
test('Actualiza correctamente los datos de un rol');

test('Falla al tratar de actualizar un rol inexistente');

test('Falla al tratar de actualizar un rol con datos invalidos');

test('Falla al tratar de actualizar un rol sin permisos');

test('Falla al tratar de actualizar su propio rol');

test('Falla al tratar de actualizar un rol del sistema');

test('Falla al tratar de actualizar un rol con permisos que no posee');

// Index
test('Devuelve correctamente el catalogo de los roles');

test('No devuelve nada cuando el filtro descarta todoso los registros de roles');

// Show
test('Muestra correctamente la información de un rol');

test('Falla al tratar de mostrar la información de un rol inexistente');

test('Falla al tratar de mostrar la información de un rol sin permisos');

// Destroy
test('Eliminar correctamente el registro de un rol');

test('Falla al tratar de eliminar un rol inexistente');

test('Falla al tratar de eliminar un rol sin permisos');

test('Falla al tratar de eliminar un rol que tiene usuarios asociados');