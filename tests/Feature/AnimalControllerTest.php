<?php

use App\Models\Animal;
use App\Models\Habitat;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    $this->role = Role::factory()->create();
    $this->user = User::factory()->create(['role_id' => $this->role->id]);
});

uses(RefreshDatabase::class);

test('Crea un animal y regresa un JSON', function () {
    $habitat = Habitat::factory()->create();
    $species = Species::factory()->create();

    $createPermission = Permission::factory()->create(['name' => 'animals.create']);
    $this->role->permissions()->sync($createPermission->id);

    $payload = [
        'habitat_id' => $habitat->id,
        'species_id' => $species->id,
        'code' => 'leo-01   ',
        'name' => 'Simba',
    ];

    $response = $this->postJson('/api/animals', $payload);

    // $response->dd();

    $response->assertStatus(Response::HTTP_CREATED)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'habitat_id',
                'species_id',
                'code',
                'name',
                'sex',
                'birth_date',
                'admission_date',
                'health_status'
            ]
        ])->assertJsonFragment([
            'code' => 'LEO-01',
            'sex' => 'unknown',
            'birth_date' => null,
            'admission_date' => date('Y-m-d'),
            'health_status' => 'healthy'
        ]);
    
    $this->assertDatabaseHas('animals', [
        'habitat_id' => $habitat->id,
        'species_id' => $species->id,
        'code' => 'LEO-01',
    ]);
});

test('Falla al crear cuando el code no es único', function () {
    $animal1 = Animal::factory()->create(['code' => 'ANI-01']);

    $createPermission = Permission::factory()->create(['name' => 'animals.create']);
    $this->role->permissions()->sync($createPermission->id);

    $payload = [
        'habitat_id' => 1,
        'species_id' => 1,
        'code' => 'ani-01   ',
        'name' => 'Simba',
    ];

    $response = $this->postJson('/api/animals', $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['code']);
});

test('Falla cuando no se envían los parametros obligatorios', function () {
    $createPermission = Permission::factory()->create(['name' => 'animals.create']);
    $this->role->permissions()->sync($createPermission->id);

    $response = $this->postJson('/api/animals', []);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['habitat_id', 'species_id', 'code', 'name']);
});

test('Falla al crear con llaves foraneas invalidas', function () {
    $createPermission = Permission::factory()->create(['name' => 'animals.create']);
    $this->role->permissions()->sync($createPermission->id);

    $payload = [
        'habitat_id' => 999,
        'species_id' => 999,
        'code' => 'ani-01   ',
        'name' => 'Simba',
    ];

    $response = $this->postJson('/api/animals', $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['habitat_id', 'species_id']);
});

test('Falla al crear si se ingresan formatos invalidos', function () {
    $habitat = Habitat::factory()->create();
    $species = Species::factory()->create();

    $createPermission = Permission::factory()->create(['name' => 'animals.create']);
    $this->role->permissions()->sync($createPermission->id);

    $payload = [
        'habitat_id' => $habitat->id,
        'species_id' => $species->id,
        'code' => 'LEO-01',
        'name' => 'Simba',
        'sex' => 'transgender',
        'birth_date' => '2028-05-12',
        'admission_date' => '2030-01-10',
        'health_status' => 'vivito y coleando'
    ];

    $response = $this->postJson('/api/animals', $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['sex', 'birth_date', 'admission_date', 'health_status']);
});

test('Retorna el listado correcto de animales.', function () {
    $createPermission = Permission::factory()->create(['name' => 'animals.view']);
    $this->role->permissions()->sync($createPermission->id);

    Animal::factory(50)->create();
    $per_page = 10;
    $page = 2;
    $offset = $page * $per_page + 1;

    $response = $this->get("/api/animals?per_page={$per_page}&page={$page}");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonCount($per_page, 'data')
        ->assertJsonPath('data.0.id', $offset);
});

test('Retorna la estructura JSON esperada para un code existente', function () {
    $createPermission = Permission::factory()->create(['name' => 'animals.view']);
    $this->role->permissions()->sync($createPermission->id);

    Animal::factory(5)->create();
    Animal::factory()->create(['code' => 'LEO-002']);

    $response = $this->get("/api/animals/LEO-002");

    $response->assertStatus(Response::HTTP_FOUND)
        ->assertJsonPath('data.0.code', 'LEO-002');
});

test('Falla al consultar con un code invalido', function () {
    $createPermission = Permission::factory()->create(['name' => 'animals.view']);
    $this->role->permissions()->sync($createPermission->id);

    Animal::factory(5)->create();

    $response = $this->get("/api/animals/LEO-999");

    $response->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonFragment(['message' => 'Código invalido.']);
});

test('Modificación correcta de campos', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('Permite conservar el mismo code si pertenece al mismo animal', function() {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('Falla si code ya esta en uso', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('Falla si se intenta actualizar un registro inexistente', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('Elimina un registro correctamente', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('Falla si se intenta eliminar un registro inexistente', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
