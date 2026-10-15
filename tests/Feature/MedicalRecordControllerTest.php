<?php

use App\Models\Animal;
use App\Models\MedicalRecord;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

use function Symfony\Component\Clock\now;

beforeEach(function () {
    $this->role = Role::factory()->create();
    $this->user = User::factory()->create(['role_id' => $this->role->id]);

    $this->actingAs($this->user, 'sanctum');
});

uses(RefreshDatabase::class);

// Store
test('Crea un registro médico correctamente', function () {
    $permission = Permission::factory()->create(['name' =>  'medical.create']);
    $this->role->permissions()->sync($permission->id);

    $animal = Animal::factory()->create();
    $now = Carbon::now();

    $payload = [
        'animal_code'       => $animal->code,
        'record_type'       => 'emergency',
        'diagnosis'         => 'Texto de diagnostico de ejemplo.',
        'treatment'         => 'Texto de tratamiento de ejmeplo.',
        'performed_at'      => $now->toIso8601String(),
        'next_checkup_at'   => $now->copy()->addWeeks(2)->toIso8601String(),
    ];

    $response = $this->postJson('/api/med_records', $payload);

    $response->assertStatus(Response::HTTP_CREATED)
        ->assertJsonPath('data.animal.name', $animal->name)
        ->assertJsonPath('data.animal.code', $animal->code)
        ->assertJsonPath('data.user.name', $this->user->name)
        ->assertJsonPath('data.record.performed_at', $now->toIso8601String())
        ->assertJsonStructure([
            'success',
            'data' => [
                'record' => [
                    'id',
                    'record_type',
                    'diagnosis',
                    'treatment',
                    'performed_at',
                    'next_checkup_at',
                ],
                'animal' => [
                    'name',
                    'code',
                ],
                'user' => [
                    'name',
                    'id',
                ]
            ]
        ]);
        
        expect($response->json('data.animal'))->toMatchArray([
            'name' => $animal->name,
            'code' => $animal->code,
        ]);

        expect($response->json('data.user'))->toMatchArray([
            'name'  => $this->user->name,
            'id'    => $this->user->uuid,
        ]);
});

test('Falla al tratar de crear con datos invalidos', function () {
    $permission = Permission::factory()->create(['name' =>  'medical.create']);
    $this->role->permissions()->sync($permission->id);

    $now = Carbon::now();

    $payload = [
        'animal_code'       => '   asdf1233   ',
        'user_uuid'         => uuid_create(),
        'diagnosis'         => 'Texto de diagnostico de ejemplo.',
        'treatment'         => 'Texto de tratamiento de ejmeplo.',
        'performed_at'      => $now->addWeeks(2)->toDateTimeString(),
        'next_checkup_at'   => $now->toDateTimeString(),
    ];

    $response = $this->postJson('/api/med_records', $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonPath('success', false)
        ->assertJsonStructure([
            'success',
            'data' => [
                'errors' => [
                    'animal_code',
                    'user_uuid',
                    'performed_at',
                    'next_checkup_at'
                ]
            ]
        ]);
});

test('Falla al tratar de crear sin permisos', function () {
    $animal = Animal::factory()->create();

    $payload = [
        'animal_code'       => $animal->code,
        'user_uuid'         => $this->user->uuid,
        'diagnosis'         => 'Texto de diagnostico de ejemplo.',
        'treatment'         => 'Texto de tratamiento de ejmeplo.',
    ];

    $response = $this->postJson('/api/med_records', $payload);

    $response->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de crear sin autenticacion', function () {
    $this->app['auth']->forgetGuards();
    $permission = Permission::factory()->create(['name' =>  'medical.create']);
    $this->role->permissions()->sync($permission->id);

    $animal = Animal::factory()->create();

    $payload = [
        'animal_code'       => $animal->code,
        'user_uuid'         => $this->user->uuid,
        'diagnosis'         => 'Texto de diagnostico de ejemplo.',
        'treatment'         => 'Texto de tratamiento de ejmeplo.',
    ];

    $response = $this->postJson('/api/med_records', $payload);

    $response->assertStatus(Response::HTTP_UNAUTHORIZED)
        ->assertJsonPath('success', false);
});

// Update
test('Actualiza correctamente la información de un registro médico', function () {
    $permission = Permission::factory()->create(['name' =>  'medical.update_own']);
    $this->role->permissions()->sync($permission->id);

    $record = MedicalRecord::factory()->create();
    $date = Carbon::now()->addWeeks(1);

    $payload = [
        'treatment'         => 'Nuevo texto de ejemplo para el tratamiento',
        'next_checkup_at'   => $date->toDateTimeString()
    ];

    $response = $this->putJson("/api/med_records/{$record->uuid}", $payload);

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonPath('data.record.record_type', 'correction')
        ->assertJsonPath('data.record.next_checkup_at', $date->toDateTimeString())
        ->assertJsonPath('data.record.treatment', $payload['treatment'])
        ->assertJsonPath('data.record.replaces_record', $record->uuid);
});

test('Falla al tratar de actualizar con datos invalidos', function () {
    $permission = Permission::factory()->create(['name' =>  'medical.update_own']);
    $this->role->permissions()->sync($permission->id);

    $date = Carbon::now();

    $record = MedicalRecord::factory()->create([
        'performed_at' => $date->addWeeks(2)->toDateTimeString()
        ]);

    $payload = [
        'record_type'       => 'tipo invalido',
        'next_checkup_at'   => $date->toDateTimeString()
    ];


    $response = $this->putJson("/api/med_records/{$record->uuid}", $payload);


    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonStructure([
            'success',
            'data' => [
                'errors' => [
                    'record_type',
                    'next_checkup_at',
                ]
            ]
        ]);
});

test('Falla al tratar de actualizar sin permisos', function () {
    $record = MedicalRecord::factory()->create();
    $date = Carbon::now()->addWeeks(1);

    $payload = [
        'treatment'         => 'Nuevo texto de ejemplo para el tratamiento',
        'next_checkup_at'   => $date->toDateTimeString()
    ];

    $response = $this->putJson("/api/med_records/{$record->uuid}", $payload);

    $response->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonPath('success', false);
});

// Index
test('Devuelve correctamente la lista de registro médicos', function () {
    $permission = Permission::factory()->create(['name' =>  'medical.view']);
    $this->role->permissions()->sync($permission->id);

    MedicalRecord::factory(50)->create();

    $animal = Animal::factory()->create();

    MedicalRecord::factory(10)->create(['animal_id' => $animal->id]);

    $filters = "?search={$animal->uuid}&page=2&per_page=5";

    $response = $this->getJson("/api/med_records{$filters}");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('data.0.animal.name', $animal->name);
});

test('No devuelve nada cuando el filtro descarta todos los registros', function () {
    $permission = Permission::factory()->create(['name' =>  'medical.view']);
    $this->role->permissions()->sync($permission->id);

    MedicalRecord::factory(60)->create();

    $filters = "?search=". uuid_create() ."&page=2&per_page=5";

    $response = $this->getJson("/api/med_records{$filters}");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonCount(0, 'data');
});

// Show
test('Devuelvel correctamente la información del registro médico', function () {
    $permission = Permission::factory()->create(['name' =>  'medical.view']);
    $this->role->permissions()->sync($permission->id);

    $record = MedicalRecord::factory()->create();

    $response = $this->getJson("/api/med_records/{$record->uuid}");

    $record->load('animals');

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonPath('data.animal.name', $record->animals->name);
});

test('Falla al consultar un registro médico inexistente', function () {
    $permission = Permission::factory()->create(['name' =>  'medical.view']);
    $this->role->permissions()->sync($permission->id);
    
    MedicalRecord::factory(10)->create();

    $response = $this->getJson("/api/med_records/". uuid_create());

    $response->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('success', false);
});

test('Falla al consultar sin permisos', function () {
    $record = MedicalRecord::factory()->create();

    $response = $this->getJson("/api/med_records/{$record->uuid}");

    $record->load('animals');

    $response->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonPath('success', false);
});