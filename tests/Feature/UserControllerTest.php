<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    $this->role = Role::factory()->create();
    $this->user = User::factory()->create(['role_id' => $this->role->id]);

    $this->actingAs($this->user, 'sanctum');
});

uses(RefreshDatabase::class);

// Store
test('Crea un usuario correctamente con todos sus datos', function () {
    $role = Role::factory()->create(['name' => 'cuidador']);
    $permission = Permission::factory()->create(['name' =>  'users.create']);
    $this->role->permissions()->sync($permission->id);

    $payload = [
        'name'      => 'pedro',
        'email'     => 'pedro@example.com',
        'password'  => 'qwerty',
        'role_name' => 'cuidador'
    ];

    $response = $this->postJson('/api/users', $payload);

    $response->assertStatus(Response::HTTP_CREATED)
        ->assertJsonFragment(['success' => true])
        ->assertJsonPath('data.name', $payload['name']);
});

test('Falla al crear un usuario por falta de permisos', function () {
    $role = Role::factory()->create(['name' => 'cuidador']);
    $payload = [
        'name'      => 'pedro',
        'email'     => 'pedro@example.com',
        'password'  => 'qwerty',
        'role_name' => 'cuidador'
    ];

    $response = $this->postJson('/api/users', $payload);

    $response->assertStatus(Response::HTTP_FORBIDDEN);
});

test('Falla al crar un usuario enviado datos erroneos', function () {
    User::factory()->create(['email' => 'pedro@example.com']);

    $payload = [
        'email'     => 'pedro@example.com',
        'role_name' => 'cuidador'
    ];

    $response = $this->postJson('/api/users', $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

// Show
test('Devuelve correctamente la información de OTRO usuario', function () {
   $user = User::factory()->create();

    $permission = Permission::factory()->create(['name' =>  'users.view_any']);
    $this->role->permissions()->sync($permission->id);

    $payload = $user->uuid;

    $response = $this->getJson("/api/users/{$payload}");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonPath('data.uuid', $payload);
});

test('Devuelve correctamente la información SU usuario y sin permisos', function () {
    $payload = $this->user->uuid;

    $response = $this->getJson("/api/users/{$payload}");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonPath('data.uuid', $payload);
});

test('Falla al consultar la información de OTRO usuario', function () {
    $user = User::factory()->create();

    $payload = $user->uuid;

    $response = $this->getJson("/api/users/{$payload}");

    $response->assertStatus(Response::HTTP_FORBIDDEN);
});

test('Falla al consultar la información de un usuario inexistente', function () {
    $permission = Permission::factory()->create(['name' =>  'users.view_any']);
    $this->role->permissions()->sync($permission->id);

    $payload = "ed8b0988-de78-4a3d-9a44-364f3db08311";

    $response = $this->getJson("/api/users/{$payload}");

    $response->assertStatus(Response::HTTP_BAD_REQUEST);
});

// Index
test('Devuelve correctamente la información de TODOS los usuarios', function () {
    $role1 = Role::factory()->create(['name' => 'cuidador']);
    $role2 = Role::factory()->create(['name' => 'veterinario']);
    User::factory(25)->create(['role_id' => $role1->id]);
    User::factory(25)->create(['role_id' => $role2->id]);

    $payload = "?per_page=2&page=3&role={$role1->name}";

    $response = $this->getJson("/api/users/{$payload}");

    $user = User::where('role_id', $role1->id)->skip(4)->firstOrFail();

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.name', $user->name);
});

test('No devuelve nada cuando el filtro descarta todos los usuarios', function () {
    User::factory(10)->create();

    $payload = '?page=20';

    $response = $this->getJson("/api/users/{$payload}");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonCount(0, 'data');
});

// Update
test('Actualiza correctamente SU propio usuario', function () {
    $payload = [
        'name'  => 'Sidney',
        'email' => 'sidney@example.com'
    ];

    $response = $this->putJson("/api/users/{$this->user->uuid}", $payload);

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment(['success' => true])
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonPath('data.email', $payload['email']);
});

test('Actualiza correctamente la información de OTRO usuario', function () {
    $permission = Permission::factory()->create(['name' => 'users.update_any']);
    $this->role->permissions()->sync($permission->id);

    $user = User::factory()->create();

    $payload = ['name'  => 'Sidney'];

    $response = $this->putJson("/api/users/{$user->uuid}", $payload);

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonPath('data.name', $payload['name']);
});

test('Falla al tratar de actualizar el rol de SU propio usuario', function () {
    $payload = [
        'role_name' => 'ejemplo'
    ];

    $response = $this->putJson("/api/users/{$this->user->uuid}", $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

// Destroy
test('Elimina el registro de un usuario correctamente', function () {
    $permission = Permission::factory()->create(['name' => 'users.delete_any']);
    $this->role->permissions()->sync($permission);

    $user = User::factory()->create();

    $response = $this->deleteJson("/api/users/{$user->uuid}");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment(['success' => true]);
});

test('Falla al tratar de eliminar un usuario inexistente', function () {
    $permission = Permission::factory()->create(['name' => 'users.delete_any']);
    $this->role->permissions()->sync($permission);

    $uuid = uuid_create();

    $response = $this->deleteJson("/api/users/{$uuid}");

    $response->assertStatus(Response::HTTP_BAD_REQUEST)
        ->assertJsonFragment(['success' => false]);
});

test('Falla al tratar de eliminar SU propia cuenta', function () {
    $permission = Permission::factory()->create(['name' => 'users.delete_any']);
    $this->role->permissions()->sync($permission);

    $response = $this->deleteJson("/api/users/{$this->user->uuid}");

    $response->assertStatus(Response::HTTP_FORBIDDEN);
});

// Login
test('Inicia sesión correctamente y obtener un token de sesión', function () {
    $this->app['auth']->forgetGuards();

    User::factory()->create(['email' => 'pedro@example.com', 'password' => 'qwerty']);

    $payload = [
        'email'     => 'pedro@example.com',
        'password'  => 'qwerty'
    ];

    $response = $this->postJson('/api/login', $payload);

    $response->assertStatus(200)
        ->assertJsonStructure(['data' => ['access_token']]);
});

test('Inicia sesión correctamente con Cookies', function () {
    $this->app['auth']->forgetGuards();
    
    $user = User::factory()->create(['email' => 'pedro@example.com', 'password' => 'qwerty']);

    $payload = [
        'email'     => 'pedro@example.com',
        'password'  => 'qwerty'
    ];

    $this->get('/sanctum/csrf-cookie');

    $response = $this->postJson('/api/login', $payload);

    $response->assertStatus(200);

    $this->assertAuthenticatedAs($user, 'web');

    $protectedResponse = $this->getJson('/api/users');
    $protectedResponse->assertStatus(200);
});

// Logout
test('Puede cerrar sesión y desactiva el token', function () {
    $this->app['auth']->forgetGuards();
    $user = User::factory()->create();

    expect(PersonalAccessToken::count())->toBe(0);
    $token = $user->createToken('session-token')->plainTextToken;
    expect(PersonalAccessToken::count())->toBe(1);

    $response = $this->withToken($token)->postJson("/api/logout");

    expect(PersonalAccessToken::count())->toBe(0);

    $this->app['auth']->forgetGuards();
    $this->withHeaders([]);
    $this->flushSession();

    $validationResponse = $this->withToken($token)->getJson('/api/users');

    $validationResponse->assertStatus(Response::HTTP_UNAUTHORIZED);
});

test('Puede cerrar sesión utilizando cookies', function () {
    $this->app['auth']->forgetGuards();
    
    $user = User::factory()->create(['email' => 'pedro@example.com', 'password' => 'qwerty']);

    $payload = [
        'email'     => 'pedro@example.com',
        'password'  => 'qwerty'
    ];

    $this->get('/sanctum/csrf-cookie');
    $this->postJson('/api/login', $payload);

    $this->assertAuthenticatedAs($user, 'web');

    $response = $this->postJson('api/logout');

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Sesión cerrada exitosamente');

    $this->app['auth']->forgetGuards();
    $this->withHeaders([]);
    $this->flushSession();
    
    $this->assertGuest('web');

    $validationResponse = $this->getJson('/api/users');
    $validationResponse->assertStatus(401);
});
