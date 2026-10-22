<?php

use App\Models\Animal;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    $this->role = Role::factory()->create();
    $this->user = User::factory()->create(['role_id' => $this->role->id]);

    $this->actingAs($this->user, 'sanctum');
});

uses(RefreshDatabase::class);

// Store
test('Registra una especie correctamente', function () {
    $permission = Permission::factory()->create(['name' =>  'species.create']);
    $this->role->permissions()->sync($permission->id);

    $payload = [
        'common_name'       => 'Perro',
        'scientific_name'   => 'Perrus Callejerus',
        'description'       => 'Un perro normal para la descripcion'
    ];

    $response = $this->postJson('/api/species', $payload);
    

    $response->assertStatus(Response::HTTP_CREATED)
        ->assertJsonPath('data.common_name', strtoupper(trim($payload['common_name'])))
        ->assertJsonStructure([
            'success',
            'data'  => [
                'common_name',
                'scientific_name',
                'description'
            ],
            'message'
        ]);
});

test('Falla al tratar de registrar una especie con datos invalidos', function () {
    $permission = Permission::factory()->create(['name' =>  'species.create']);
    $this->role->permissions()->sync($permission->id);

    $species = Species::factory()->create();

    $payload = [
        'common_name'  => strtolower(" {$species->name} ")
    ];

    $response = $this->postJson('/api/species', $payload);


    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonStructure([
            'success',
            'message'   => [
                'common_name',
                'scientific_name',
                'description'
            ]
        ]);
});

test('Falla al tratar de registrar una especie sin permisos', function () {
    $payload = [
        'common_name'       => 'Perro',
        'scientific_name'   => 'Perro cientifico',
        'description'       => 'Un perro normal para la descripcion'
    ];

    $response = $this->postJson('/api/species', $payload);

    $response->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de registrar una especie sin autenticación', function () {
    $this->app['auth']->forgetGuards();
    $permission = Permission::factory()->create(['name' =>  'species.create']);
    $this->role->permissions()->sync($permission->id);

    $payload = [
        'common_name'       => 'Perro',
        'scientific_name'   => 'Perro cientifico',
        'description'       => 'Un perro normal para la descripcion'
    ];

    $response = $this->postJson('/api/species', $payload);

    $response->assertStatus(Response::HTTP_UNAUTHORIZED)
        ->assertJsonPath('success', false);
});

// Update
test('Actualiza el registro de una especie correctamente', function () {
    $permission = Permission::factory()->create(['name' =>  'species.update']);
    $this->role->permissions()->sync($permission->id);

    $species = Species::factory()->create();

    $payload = [
        'common_name'       => ' Gato  ',
        'scientific_name'   => 'Gato malvado',
        'description'       => 'Descripcion actualizada'
    ];

    $response = $this->putJson("/api/species/{$species->id}", $payload);


    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonPath('data.common_name', strtoupper(trim($payload['common_name'])));
});

test('Falla al tratar de actualizar el registro de una especie inexistente', function () {
    $permission = Permission::factory()->create(['name' =>  'species.update']);
    $this->role->permissions()->sync($permission->id);

    $payload = [
        'common_name'          => 'Gato',
        'description'   => 'Descripcion actualizada'
    ];

    $response = $this->putJson("/api/species/5", $payload);

    $response->assertStatus(Response::HTTP_BAD_REQUEST)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de actualizar el registro de una especie con datos invalidos', function () {
    $permission = Permission::factory()->create(['name' =>  'species.update']);
    $this->role->permissions()->sync($permission->id);

    $species1 = Species::factory()->create();
    $species2 = Species::factory()->create();


    $payload = [
        'common_name'          => $species2->common_name,
        'description'   => 123213123
    ];

    $response = $this->putJson("/api/species/{$species1->id}", $payload);


    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonStructure([
            'success',
            'message'   => [
                'common_name',
                'description'
            ]
        ]);
});

test('Falla al tratar de actualizar el registro de una especie sin permisos', function () {
    $species = Species::factory()->create();

    $payload = [
        'common_name'          => 'Gato',
        'description'   => 'Descripcion actualizada'
    ];

    $response = $this->putJson("/api/species/{$species->id}", $payload);

    $response->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonPath('success', false);
});

// Index
test('Devuelve correctamente el catalogo de especies', function () {
    $permission = Permission::factory()->create(['name' => 'species.view']);
    $this->role->permissions()->sync($permission->id);

    Species::factory(30)->create();

    $species = Species::where('id', 6)->firstOrFail();

    $filters = 'per_page=5&page=2';

    $response = $this->getJson("/api/species?{$filters}");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('data.0.common_name', $species->common_name);
});

test('No devuelve nada cuando el filtro descarta todos los registros', function () {
    $permission = Permission::factory()->create(['name' => 'species.view']);
    $this->role->permissions()->sync($permission->id);

    Species::factory(30)->create();

    $filters = 'per_page=5&page=2&search=danta';

    $response = $this->getJson("/api/species?{$filters}");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonCount(0, 'data');
});

// Show
test('Devuelve correctamente la información del registro de la especie', function () {
    $permission = Permission::factory()->create(['name' => 'species.view']);
    $this->role->permissions()->sync($permission->id);

    $species = Species::factory()->create();

    Animal::factory(10)->create(['species_id'    => $species->id]);

    $response = $this->getJson("/api/species/{$species->id}");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonCount(10, 'data.animals')
        ->assertJsonPath('data.especies.common_name', $species->name);
});

test('Falla al consultar la información de un registro inexistente', function () {
    $permission = Permission::factory()->create(['name' => 'species.view']);
    $this->role->permissions()->sync($permission->id);

    $response = $this->getJson("/api/species/88");

    $response->assertStatus(Response::HTTP_BAD_REQUEST)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de consultar la información de un registro de especie sin permisos', function () {
    $species = Species::factory()->create();

    $response = $this->getJson("/api/species/{$species->id}");

    $response->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonPath('success', false);
});

// Destroy
test('Elimina el registro de una especie correctamente', function () {
    $permission = Permission::factory()->create(['name' => 'especies.delete']);
    $this->role->permissions()->sync($permission->id);

    $species = Species::factory()->create();

    $response = $this->deleteJson("/api/species/{$species->id}");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonPath('success', true);
});

test('Falla al tratar de eliminar un registro de una especie inexistente', function () {
    $permission = Permission::factory()->create(['name' => 'especies.delete']);
    $this->role->permissions()->sync($permission->id);

    $response = $this->deleteJson("/api/species/99");

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de eliminar un registro de una especie sin permisos', function () {
    $species = Species::factory()->create();

    $response = $this->deleteJson("/api/species/{$species->id}");

    $response->assertStatus(Response::HTTP_UNAUTHORIZED)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de eliminar un registro de una especie en uso', function () {
    $permission = Permission::factory()->create(['name' => 'especies.delete']);
    $this->role->permissions()->sync($permission->id);

    $species = Species::factory()->create();

    Animal::factory(10)->create(['species_id'    => $species->id]);

    $response = $this->deleteJson("/api/species/{$species->id}");

    $response->assertStatus(Response::HTTP_BAD_REQUEST)
        ->assertJsonPath('success', false);
});
