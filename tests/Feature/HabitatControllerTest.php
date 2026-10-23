<?php

use App\Models\Animal;
use App\Models\Habitat;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    $this->role = Role
    ::factory()->create();
    $this->user = User::factory()->create(['role_id' => $this->role->id]);

    $this->actingAs($this->user, 'sanctum');
});

uses(RefreshDatabase::class);

// Store
test('Registra correctamente un habitat', function () {
    $permission = Permission::factory()->create(['name' => 'habitats.create']);
    $this->role->permissions()->sync($permission->id);

    $payload = [
        'name'      => '    Cuarentena-01    ',
        'type'      => 'terrestrial',
        'capacity'  => 3,
        'status'    => 'available',
    ];

    $response = $this->postJson('/api/habitats', $payload);

    $response->assertStatus(Response::HTTP_CREATED)
        ->assertJsonPath('data.0.name', strtoupper(trim($payload['name'])))
        ->assertJsonStructure([
            'success',
            'data'  => [
                'name',
                'type',
                'capacity',
                'status'
            ],
            'message'
        ]);
});

test('Falla al tratar de registrar un habitat con datos invalidos', function () {
    $permission = Permission::factory()->create(['name' => 'habitats.create']);
    $this->role->permissions()->sync($permission->id);

    $habitat1 = Habitat::factory()->create();

    $payload = [
        'name'      => $habitat1->name,
        'type'      => 'tipo-invalido',
        'capacity'  => '123456789',
        'status'    => 'invalid',

    ];

    $response = $this->postJson('/api/habitats', $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonStructure([
            'success',
            'message'   => [
                'name',
                'type',
                'capacity',
                'status'
            ]
        ]);
});

test('Falla al tratar de registrar un habitat sin permisos', function () {
    $payload = [
        'name'      => '    Cuarentena-01    ',
        'type'      => 'terrestrial',
        'capacity'  => 3,
        'status'    => 'available',
    ];

    $response = $this->postJson('/api/habitats', $payload);

    $response->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de registrar un habitat sin autenticación', function () {
    $this->app['auth']->forgetGuards();
    $permission = Permission::factory()->create(['name' => 'habitats.create']);
    $this->role->permissions()->sync($permission->id);

    $payload = [
        'name'      => '    Cuarentena-01    ',
        'type'      => 'terrestrial',
        'capacity'  => 3,
        'status'    => 'available',
    ];

    $response = $this->postJson('/api/habitats', $payload);

    $response->assertStatus(Response::HTTP_UNAUTHORIZED)
        ->assertJsonPath('success', false);
});

// Update
test('Actualiza el registro de un habitat correctamente', function () {
    $permission = Permission::factory()->create(['name' => 'habitats.update']);
    $this->role->permissions()->sync($permission->id);

    $habitat1 = Habitat::factory()->create();

    $payload = [
        'name'      => '    Un Nombre nuevo   ',
        'type'      => 'aviary',
        'capacity'  => 60,
        'status'    => 'maintenance',
    ];

    $response = $this->putJson("/api/habitats/{$habitat1->id}", $payload);

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonPath('data.name', strtoupper(trim($payload['name'])))
        ->assertJsonPath('data.status', $payload['status']);
});

test('Falla al tratar de actualizar el registro de un habitat inexistente', function () {
    $permission = Permission::factory()->create(['name' => 'habitats.update']);
    $this->role->permissions()->sync($permission->id);

    $payload = [
        'name'      => '    Un Nombre nuevo   ',
        'type'      => 'aviary',
        'capacity'  => 60,
        'status'    => 'maintenance',
    ];

    $response = $this->putJson("/api/habitats/999", $payload);

    $response->assertStatus(Response::HTTP_BAD_REQUEST)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de actualizar el registro de un habitat con datos invalidos', function () {
    $permission = Permission::factory()->create(['name' => 'habitats.update']);
    $this->role->permissions()->sync($permission->id);

    $habitat1 = Habitat::factory()->create();
    $habitat2 = Habitat::factory()->create();

    $payload = [
        'name'      => $habitat1->name,
        'type'      => 'tipo-invalido',
        'capacity'  => '123456789',
        'status'    => 'invalid',

    ];

    $response = $this->putJson("/api/habitats/{$habitat2->id}", $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de actualizar el registro de un habitat sin permisos', function () {
    $habitat1 = Habitat::factory()->create();

    $payload = [
        'name'      => '    Un Nombre nuevo   ',
        'type'      => 'aviary',
        'capacity'  => 60,
        'status'    => 'maintenance',
    ];

    $response = $this->putJson("/api/habitats/{$habitat1->id}", $payload);

    $response->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de actualizar la capacidad de un registro de habitat menor a la cantidad de animales asociados', function () {
    $permission = Permission::factory()->create(['name' => 'habitats.update']);
    $this->role->permissions()->sync($permission->id);

    $habitat1 = Habitat::factory()->create(['capacity'  => 10]);

    $payload = [
        'capacity'  => 5,
    ];

    Animal::factory($habitat1->capcity)->create(['habitat_id' => $habitat1->id]);

    $response = $this->putJson("/api/habitats/999", $payload);

    $response->assertStatus(Response::HTTP_BAD_REQUEST)
        ->assertJsonPath('success', false);
});

// Index
test('Devuelve correctamente el catalogo de habitats');

test('No devuelve nada cuando el filtro descarta todoso los registros de habitats');

// Show
test('Devuelve correctamente la información del registro del habitat');

test('Falla al consultar la información de un registro inexistente');

test('Falla al tratar de consultar la información de un registro de habitat sin permisos');

// Destroy
test('Eliminar el registro de un habitat correctamente');

test('Falla al tratar de eliminar un registro de un habitat inexistente');

test('Falla al tratar de eliminar un registro de un habitat sin permisos');

test('Falla al tratar de eliminar un registro de un habitat en uso');