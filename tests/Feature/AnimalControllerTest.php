<?php

use App\Models\Animal;
use App\Models\Habitat;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Species;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    $this->role = Role::factory()->create();
    $this->user = User::factory()->create(['role_id' => $this->role->id]);

    $this->actingAs($this->user);
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
        ->assertJsonValidationErrors(['code'], 'message');
});

test('Falla cuando no se envían los parametros obligatorios', function () {
    $createPermission = Permission::factory()->create(['name' => 'animals.create']);
    $this->role->permissions()->sync($createPermission->id);

    $response = $this->postJson('/api/animals', []);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['habitat_id', 'species_id', 'code', 'name'], 'message');
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
        ->assertJsonValidationErrors(['habitat_id', 'species_id'], 'message');
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
        ->assertJsonValidationErrors(['sex', 'birth_date', 'admission_date', 'health_status'], 'message');
});

test('Retorna el listado correcto de animales.', function () {
    $createPermission = Permission::factory()->create(['name' => 'animals.view']);
    $this->role->permissions()->sync($createPermission->id);

    $animals = Animal::factory(50)->create();
    $per_page = 10;
    $page = 2;
    $offset = $page * $per_page + 1;
    $expectedFirstAnimalInPage = $animals->get(20);

    $response = $this->get("/api/animals?per_page={$per_page}&page={$page}");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonCount($per_page, 'data')
        ->assertJsonPath('data.0.id', $expectedFirstAnimalInPage->id);
});

// Show
test('Devuelve correctamente la información de un animal', function () {
    $permission = Permission::factory()->create(['name' => 'animals.view']);
    $this->role->permissions()->sync($permission);

    $animal = Animal::factory()->create();

    $response = $this->getJson("/api/animals/{$animal->code}");

    $response->assertStatus(Response::HTTP_FOUND)
        ->assertJsonPath('data.code', $animal->code)
        ->assertJsonStructure([
            'success',
            'data'  => [
                'name',
                'code',
                'sex',
                'health_status',
                'birth_date',
                'admission_date',
                'species'   => [
                    'id',
                    'common_name',
                ],
                'habitat'   => [
                    'id',
                    'name',
                ],
            ],
            'message'
        ]);
});

test('Falla al tratar de consultar la información de un animal sin permisos', function () {
    $animal = Animal::factory()->create();

    $response = $this->getJson("/api/animals/{$animal->code}");

    $reponse->assertStatus(Reponse::HTTP_FORBIDDEN)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de consultar la información de un animal inexistente', function () {
    $permission = Permission::factory()->create(['name' => 'animals.view']);
    $this->role->permissions()->sync($permission);

    $response = $this->getJson("/api/animals/999");

    $response->assertStatus(Response::HTTP_BAD_REQUEST)
        ->assertJsonPath('success', false);
});

// Update
test('Actualiza correctamente los datos de un animal', function () {
    $createPermission = Permission::factory()->create(['name' => 'animals.update']);
    $this->role->permissions()->sync($createPermission->id);

    $animal = Animal::factory()->create();

    $payload = [
        'code'              => 'leo-999   ',
        'name'              => 'Simba',
        'sex'               => 'female',
        'birth_date'        => '2000-01-01',
        'admission_date'    => '2020-01-01',
        'health_status'     => 'quarantine'
    ];

    $response = $this->putJson("/api/animals/{$animal->code}", $payload);


    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonPath('data.health_status', $payload['health_status'])
        ->assertJsonStructure([
            'data'  => [
                'name',
                'code',
                'sex',
                'health_status',
                'birth_date',
                'admission_date',
                'species',
                'habitat'
            ],
            'success',
            'message'
        ]);
});

test('Permite conservar el mismo code si pertenece al mismo animal', function() {
    $createPermission = Permission::factory()->create(['name' => 'animals.update']);
    $this->role->permissions()->sync($createPermission->id);

    $animal = Animal::factory()->create();

    $payload = [
        'code'  => $animal->code
    ];

    $response = $this->putJson("/api/animals/{$animal->code}", $payload);

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonPath('data.code', $animal->code);
});

test('Falla si code ya esta en uso', function () {
    $createPermission = Permission::factory()->create(['name' => 'animals.update']);
    $this->role->permissions()->sync($createPermission->id);

    $animal1 = Animal::factory()->create();
    $animal2 = Animal::factory()->create();

    $payload = [
        'code'  => $animal2->code
    ];

    $response = $this->putJson("/api/animals/{$animal1->code}", $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonPath('success', false);
});

test('Falla si se intenta actualizar un registro inexistente', function () {
    $createPermission = Permission::factory()->create(['name' => 'animals.update']);
    $this->role->permissions()->sync($createPermission->id);

    $payload = [
        'code'  => 'leo-002   '
    ];

    $response = $this->putJson('api/animals/LEO-777', $payload);

    $response->assertStatus(Response::HTTP_BAD_REQUEST)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de asignar un habitat lleno a un animal', function () {
    $permission = Permission::factory()->create(['name' => 'animals.update']);
    $this->role->permissions()->sync($permission);

    $habitat1 = Habitat::factory()->create(['capacity'  => 5]);
    $habitat2 = Habitat::factory()->create();

    Animal::factory(5)->create(['habitat_id'    => $habitat1->id]);

    $animal = Animal::factory()->create(['habitat_id'   => $habitat2->id]);

    $payload = [
        'habitat_id'    => $habitat1->id
    ];

    $reponse = $this->putJson("/api/animals/{$animal->code}", $payload);

    $reponse->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de actualizar un animal con datos invalidos', function () {
    $permission = Permission::factory()->create(['name' => 'animals.update']);
    $this->role->permissions()->sync($permission);

    $date = Carbon::now();

    $animal1 = Animal::factory()->create();
    $animal2 = Animal::factory()->create(['admission_date'  => $date->toIso8601String()]);

    $payload = [
        'birth_date'    => $date->addCenturies(1),
        'code'          => $animal1->code,
        'sex'           => 'sexo invalido',
        'health_status' => 'estado invalido',
        'name'          => 1,
        'species_id'    => 'hola',
        'habitat_id'    => 'id'
    ];

    $reponse = $this->putJson("/api/animals/{$animal2->code}", $payload);

    $reponse->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonPath('success', false)
        ->assertJsonStructure([
            'message'   => [
                'name',
                'sex',
                'code',
                'birth_date',
                'health_status',
                'species_id',
                'habitat_id'
            ]
        ]);
});

// Destroy
test('Elimina un registro correctamente', function () {
    $createPermission = Permission::factory()->create(['name' => 'animals.delete']);
    $this->role->permissions()->sync($createPermission->id);

    $animal = Animal::factory()->create();

    $response = $this->deleteJson("api/animals/{$animal->code}");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonPath('success', true);
});

test('Falla si se intenta eliminar un registro inexistente', function () {
    $createPermission = Permission::factory()->create(['name' => 'animals.delete']);
    $this->role->permissions()->sync($createPermission->id);

    $response = $this->deleteJson('api/animals/LEO-999');

    $response->assertStatus(Response::HTTP_BAD_REQUEST)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de eliminar un animal sin permisos', function () {
    $animal = Animal::factory()->create();

    $response = $this->deleteJson("api/animals/{$animal->code}");

    $response->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonPath('success', false);
});
