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

// Store
test('Crea correctamente un animal', function () {
    $createPermission = Permission::factory()->create(['name' => 'animals.create']);
    $this->role->permissions()->sync($createPermission->id);

    $habitat = Habitat::factory()->create();
    $species = Species::factory()->create();

    $payload = [
        'name'              => '      LEON viejo     ',
        'species_id'        => $species->id,
        'habitat_id'        => $habitat->id,
        'code'              => '       LEO-999      ',
        'sex'               => 'male',
        'birth_date'        => Carbon::now()->subDays(7)->toIso8601String(),
        'admission_date'    => Carbon::today()->toIso8601String(),
        'health_status'     => 'treatment',
    ];

    $response = $this->postJson("/api/animals", $payload);


    $response->assertStatus(Response::HTTP_CREATED)
        ->assertJsonPath('data.name', strtoupper(trim($payload['name'])))
        ->assertJsonPath('data.code', strtoupper(trim($payload['code'])));
});

test('Falla al tratar de crear un animal sin permisos', function (){
    $habitat = Habitat::factory()->create();
    $species = Species::factory()->create();

    $payload = [
        'name'              => '      LEON viejo     ',
        'species_id'        => $species->id,
        'habitat_id'        => $habitat->id,
        'code'              => '       LEO-999      ',
        'sex'               => 'male',
        'birth_date'        => Carbon::now()->subDays(7)->toIso8601String(),
        'admission_date'    => Carbon::today()->toIso8601String(),
        'health_status'     => 'treatment',
    ];

    $response = $this->postJson("/api/animals", $payload);

    $response->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de crear un animal sin autenticacion', function () {
    $this->app['auth']->forgetGuards();
    $createPermission = Permission::factory()->create(['name' => 'animals.create']);
    $this->role->permissions()->sync($createPermission->id);

    $habitat = Habitat::factory()->create();
    $species = Species::factory()->create();

    $payload = [
        'name'              => '      LEON viejo     ',
        'species_id'        => $species->id,
        'habitat_id'        => $habitat->id,
        'code'              => '       LEO-999      ',
        'sex'               => 'male',
        'birth_date'        => Carbon::now()->subDays(7)->toIso8601String(),
        'admission_date'    => Carbon::today()->toIso8601String(),
        'health_status'     => 'treatment',
    ];

    $response = $this->postJson("/api/animals", $payload);

    $response->assertStatus(Response::HTTP_UNAUTHORIZED)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de crear un animal con datos invalidos', function (){
    $permission = Permission::factory()->create(['name' => 'animals.create']);
    $this->role->permissions()->sync($permission);

    $animal1 = Animal::factory()->create();
    
    $payload = [
        'name'              => 99,
        'species_id'        => 99,
        'habitat_id'        => 99,
        'code'              => $animal1->code,
        'sex'               => 'mal ae',
        'birth_date'        => Carbon::today()->addCenturies(1)->toIso8601String(),
        'admission_date'    => Carbon::now()->addDays(7)->toIso8601String(),
        'health_status'     => 'a treatment',
    ];

    $response = $this->postJson("/api/animals", $payload);
    

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonPath('success', false)
        ->assertJsonStructure([
            'success',
            'message'   => [
                'name',
                'species_id',
                'habitat_id',
                'code',
                'sex',
                'birth_date',
                'admission_date',
                'health_status'
            ],
            'data'
        ]);
});

test('Falla al tratar de crear un animal en un habitat lleno', function () {
    $createPermission = Permission::factory()->create(['name' => 'animals.create']);
    $this->role->permissions()->sync($createPermission->id);

    $habitat = Habitat::factory()->create(['capacity' => 1]);
    $species = Species::factory()->create();
    Animal::factory()->create(['habitat_id' => $habitat->id]);

    $payload = [
        'name'              => '      LEON viejo     ',
        'species_id'        => $species->id,
        'habitat_id'        => $habitat->id,
        'code'              => '       LEO-999      ',
        'sex'               => 'male',
        'birth_date'        => Carbon::now()->subDays(7)->toIso8601String(),
        'admission_date'    => Carbon::today()->toIso8601String(),
        'health_status'     => 'treatment',
    ];

    $response = $this->postJson("/api/animals", $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
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

    $response = $this->putJson("/api/animals/{$animal->code}", $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de actualizar un animal con datos invalidos', function () {
    $permission = Permission::factory()->create(['name' => 'animals.update']);
    $this->role->permissions()->sync($permission);

    $animal1 = Animal::factory()->create();
    $animal2 = Animal::factory()->create(['admission_date'  => Carbon::now()->toDateTimeString()]);

    $payload = [
        'birth_date'    => Carbon::now()->addCenturies(1)->toDateTimeString(),
        'code'          => $animal1->code,
        'sex'           => 'sexo invalido',
        'health_status' => 'estado invalido',
        'name'          => 1,
        'species_id'    => 'hola',
        'habitat_id'    => 'id'
    ];

    $response = $this->putJson("/api/animals/{$animal2->code}", $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
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

// Index
test('Devuelve correctamente el catalogo de animales', function () {
    $permission = Permission::factory()->create(['name' => 'animals.view']);
    $this->role->permissions()->sync($permission);

    Animal::factory(50)->create();

   $animal = Animal::orderBy('created_at', 'desc')->orderBy('id', 'asc')->skip(5)->firstOrFail();

    $filters = "per_page=5&page=2";

    $response = $this->getJson("/api/animals?{$filters}");


    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('data.0.code', $animal->code);
});

test('No devuevle nada cuando los filtros descartan todo el catalogo de animales', function () {
    $permission = Permission::factory()->create(['name' => 'animals.view']);
    $this->role->permissions()->sync($permission);

    Animal::factory(50)->create();

     $filters = 'per_page=5&page=2&search=LEO-999';

    $response = $this->getJson("/api/animals?{$filters}");


    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonCount(0, 'data');
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

    $response->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de consultar la información de un animal inexistente', function () {
    $permission = Permission::factory()->create(['name' => 'animals.view']);
    $this->role->permissions()->sync($permission);

    $response = $this->getJson("/api/animals/999");

    $response->assertStatus(Response::HTTP_BAD_REQUEST)
        ->assertJsonPath('success', false);
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
