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

// Index
test('Devuelve correctamente el listado de los permisos del sistema con el permiso roles.update', function () {
    $permission = Permission::factory()->create(['name' => 'roles.update']);
    $this->role->permissions()->sync($permission->id);

    Permission::factory(4)->create();

    $response = $this->getJson('/api/permissions');

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonCount(5, 'data')
        ->assertJsonFragments([
            'data'  => [
                'name' => $permission->name,
            ]
        ]);
});

test('Devuelve correctamente el listado de los permisos del sistema con el permiso roles.create', function () {
    $permission = Permission::factory()->create(['name' => 'roles.create']);
    $this->role->permissions()->sync($permission->id);

    Permission::factory(4)->create();

    $response = $this->getJson('/api/permissions');

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonCount(5, 'data')
        ->assertJsonFragments([
            'data'  => [
                'name' => $permission->name,
            ]
        ]);
});

test('Falla al tratar de consultar los permisos sin permisos', function () {
    Permission::factory(4)->create();

    $response = $this->getJson('/api/permissions');

    $response->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonPath('success', false);
});