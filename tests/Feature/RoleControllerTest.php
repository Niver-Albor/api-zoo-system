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

    $permissionsToAssign = collect($permissionsResponse->json('data'))->take(2)->pluck('id')->toArray();

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
        'permissions_id'    => [99],
    ];

    $roleResponse = $this->postJson('/api/roles', $payload);


    $roleResponse->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonStructure([
            'message'   => [
                'name',
                'description',
                'permissions_id.0'
            ],
            'success'
        ]);
});

test('Falla al tratar de crear un rol sin los permisos adecuados', function () {
    Permission::factory(5)->create();

    // Necesito sacar los IDs de los permisos por mi cuenta ya que la consulta a la API GET/permissions tambien fallaría.
    $allPermissionIds = Permission::all()->pluck('id');

    $payload = [
        'name'              => '        nombre nuevo       ',
        'description'       => 'descripcion',
        'permissions_id'    => $allPermissionIds
    ];

    $response = $this->postJson(('/api/roles'), $payload);

    $response->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de crear un rol sin autenticación', function () {
    $permission = Permission::factory()->create(['name' => 'roles.create']);
    Permission::factory(5)->create();
    $allPermissionIds = Permission::all()->pluck('id');
    $this->role->permissions()->sync($allPermissionIds); // Necesita tener todos los permisos si pretende asignarlos a un rol.

    $permissionsResponse = $this->getJson('/api/permissions');

    // Devuelve los permisos correctamente.
    $permissionsResponse->assertStatus(Response::HTTP_OK);

    $permissionsToAssign = collect($permissionsResponse->json('data'))->take(2)->pluck('id')->toArray();

    $payload = [
        'name'              => '      Rol nuevo del TEST    ',
        'description'       => 'descripcion del rol',
        'permissions_id'    => $permissionsToAssign
    ];

    $this->app['auth']->forgetGuards();

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

    $permissionsToAssign = collect($permissionsResponse->json('data'))->take(2)->pluck('id')->toArray();

    $payload = [
        'name'              => '      Rol nuevo del TEST    ',
        'description'       => 'descripcion del rol',
        'permissions_id'    => $permissionsToAssign
    ];

    $roleResponse = $this->postJson('/api/roles', $payload);


    $roleResponse->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonPath('success', false);
});

// Update
test('Actualiza correctamente los datos de un rol', function () {
    Permission::factory()->create(['name' => 'roles.update']);
    Permission::factory()->create(['name' => 'roles.create']); // Para poder crear el rol con la api POST/roles
    Permission::factory(5)->create();

    $allPermissionIds = Permission::all()->pluck('id');
    $this->role->permissions()->sync($allPermissionIds);

    // Obtiene los permisos de la api GET/permissions
    $permissionsResponse = $this->getJson('/api/permissions');

    $permissionsResponse->assertStatus(Response::HTTP_OK); // Valida que haya devuelto los permisos

    $permissionsToAssign = collect($permissionsResponse->json('data'))->take(2)->pluck('id')->toArray();

    $payload = [
        'name'  => "    Nombre Actualizado      ",
        'description'   => 'Descripcion',
        'permissions_id'    => $permissionsToAssign
    ];

    $storeResponse = $this->postJson('/api/roles', $payload);

    $storeResponse->assertStatus(Response::HTTP_CREATED); // Valida que haya creado el rol.

    $roleId = $storeResponse->json('data.id');

    $payload = [
        'name'  => '           nuevo nombre        ',
        'description'   => 'Nueva descripcion',
        'permissions_id'    => $allPermissionIds
    ];

    $updateResponse = $this->putJson("/api/roles/{$roleId}", $payload);

    $updateResponse->assertStatus(Response::HTTP_OK)
        ->assertJsonPath('data.name', strtoupper(trim($payload['name'])))
        ->assertJsonCount(7, 'data.permissions');
});

test('Falla al tratar de actualizar un rol inexistente', function () {
    $permission = Permission::factory()->create(['name' => 'roles.update']);
    $this->role->permissions()->sync($permission);

    // Obtiene los permisos de la api GET/permissions
    $permissionsResponse = $this->getJson('/api/permissions');

    $permissionsResponse->assertStatus(Response::HTTP_OK); // Valida que haya devuelto los permisos

    $permissionsToAssign = collect($permissionsResponse->json('data'))->take(2)->pluck('id')->toArray();

    $payload = [
        'name'  => "         nombre de rol ",
        'description'   => 'Descripcion',
        'permissions_id'    => $permissionsToAssign
    ];

    $response = $this->putJson('/api/roles/999', $payload);

    $response->assertStatus(Response::HTTP_BAD_REQUEST)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de actualizar un rol con datos invalidos', function () {
    Permission::factory()->create(['name' => 'roles.update']);
    Permission::factory()->create(['name' => 'roles.create']); // Para poder crear el rol con la api POST/roles
    Permission::factory(5)->create();

    $allPermissionIds = Permission::all()->pluck('id');
    $this->role->permissions()->sync($allPermissionIds);

    // Obtiene los permisos de la api GET/permissions
    $permissionsResponse = $this->getJson('/api/permissions');

    $permissionsResponse->assertStatus(Response::HTTP_OK); // Valida que haya devuelto los permisos

    $permissionsToAssign = collect($permissionsResponse->json('data'))->take(2)->pluck('id')->toArray();

    $payload = [
        'name'  => 'nombre',
        'description'   => 'Descripcion',
        'permissions_id'    => $permissionsToAssign
    ];

    $storeResponse = $this->postJson('/api/roles', $payload);

    $storeResponse->assertStatus(Response::HTTP_CREATED); // Valida que haya creado el rol.

    $roleId = $storeResponse->json('data.id');

    $payload = [
        'name'  => $this->role->name,
        'description'   => 99999999,
        'permissions_id'    => [99,88]
    ];

    $updateResponse = $this->putJson("/api/roles/{$roleId}", $payload);

    $updateResponse->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonPath('success', false)
        ->assertJsonStructure([
            'message'   => [
                'name',
                'description',
                'permissions_id.0'
            ],
            'success'
        ]);
});

test('Falla al tratar de actualizar un rol sin permisos', function () {
    Permission::factory()->create(['name' => 'roles.create']);
    Permission::factory(5)->create();
    $allPermissionIds = Permission::all()->pluck('id');
    $this->role->permissions()->sync($allPermissionIds);

    // Obtiene los permisos de la api GET/permissions
    $permissionsResponse = $this->getJson('/api/permissions');

    $permissionsResponse->assertStatus(Response::HTTP_OK); // Valida que haya devuelto los permisos

    $permissionsToAssign = collect($permissionsResponse->json('data'))->take(2)->pluck('id')->toArray();

    $payload = [
        'name'  => 'nombre',
        'description'   => 'Descripcion',
        'permissions_id'    => $permissionsToAssign
    ];

    $storeResponse = $this->postJson('/api/roles', $payload);

    $storeResponse->assertStatus(Response::HTTP_CREATED); // Valida que haya creado el rol.

    $roleId = $storeResponse->json('data.id');

    $payload = [
        'name'  => '      nombre nuevo',
        'description'   => 'descripcion nueva',
        'permissions_id'    => $allPermissionIds
    ];

    $updateResponse = $this->putJson("/api/roles/{$roleId}", $payload);

    $updateResponse->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de actualizar su propio rol', function () {
    $permission = Permission::factory()->create(['name' => 'roles.update']);
    $this->role->permissions()->sync($permission);

    $payload = [
        'name'  => 'nombre de ejemplo que no puede generar la factory, 100% real no fake',
        'permissions_id'    => [$permission->id],
    ];

    $roleId = $this->role->id;

    $response = $this->putJson("/api/roles/{$roleId}", $payload);
    

    $response->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de actualizar un rol del sistema', function () {
    $permission = Permission::factory()->create(['name' => 'roles.update']);
    $this->role->permissions()->sync($permission);

    $payload = [
        'name'  => 'nombre de ejemplo que no puede generar la factory, 100% real no fake',
        'permissions_id'    => [$permission->id],
    ];

    $systemRole = Role::factory()->create(['is_system'  => true]);

    $response = $this->putJson("/api/roles/{$systemRole->id}", $payload);

    $response->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de actualizar un rol con permisos que no posee', function () {
    Permission::factory()->create(['name' => 'roles.update']);
    Permission::factory()->create(['name' => 'roles.create']); // Para poder crear el rol con la api POST/roles

    $allPermissionIds = Permission::all()->pluck('id');
    $this->role->permissions()->sync($allPermissionIds);

    $payload = [
        'name'  => 'nombre',
        'description'  => 'descripcion',
        'permissions_id'    => $allPermissionIds
    ];

    $storeResponse = $this->postJson('/api/roles', $payload);

    $storeResponse->assertStatus(Response::HTTP_CREATED); // Valida que haya creado el rol.

    $roleId = $storeResponse->json('data.id');

    Permission::factory(5)->create();

    // Obtiene los permisos de la api GET/permissions
    $permissionsResponse = $this->getJson('/api/permissions');

    $permissionsResponse->assertStatus(Response::HTTP_OK); // Valida que haya devuelto los permisos

    $permissionsToAssign = collect($permissionsResponse->json('data'))->pluck('id')->toArray();

    $payload = [
        'permissions_id'    => $permissionsToAssign
    ];

    $updateResponse = $this->putJson("/api/roles/{$roleId}", $payload);

    $updateResponse->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de revocar un permiso que no posee', function () {
    Permission::factory()->create(['name' => 'roles.update']);
    Permission::factory()->create(['name' => 'roles.create']); // Para poder crear el rol con la api POST/roles

    $fewPermissions = Permission::all()->pluck('id'); // Para más adelante XD.

    Permission::factory(5)->create();
    $allPermissionIds = Permission::all()->pluck('id');
    $this->role->permissions()->sync($allPermissionIds);

    $payload = [
        'name'  => 'nombre',
        'description'  => 'descripcion',
        'permissions_id'    => $allPermissionIds
    ];

    $storeResponse = $this->postJson('/api/roles', $payload);

    $storeResponse->assertStatus(Response::HTTP_CREATED); // Valida que haya creado el rol.

    $roleId = $storeResponse->json('data.id');

    // Elimina todos los permisos al usuario actual, y le agrega solo el de CREATE y UPDATE.
    $this->role->permissions()->sync($fewPermissions);

    $payload = [
        'permissions_id'    => $fewPermissions
    ];

    $updateResponse = $this->putJson("/api/roles/{$roleId}", $payload);

    $updateResponse->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonPath('success', false);
});

// Index
test('Devuelve correctamente el catalogo de los roles', function () {
    $permission = Permission::factory()->create(['name' => 'roles.view']);
    $this->role->permissions()->sync($permission);

    Role::factory(20)->create();

    $role = Role::skip(5)->firstOrFail();

    $filters = "per_page=5&page=2";

    $response = $this->getJson("/api/roles?{$filters}");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('data.0.name', $role->name);
});

test('No devuelve nada cuando el filtro descarta todoso los registros de roles', function () {
    $permission = Permission::factory()->create(['name' => 'roles.view']);
    $this->role->permissions()->sync($permission);

    Role::factory(20)->create();

    $filters = "per_page=5&page=2&search=papadelospollitos";

    $response = $this->getJson("/api/roles?{$filters}");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonCount(0, 'data');
});

// Show
test('Muestra correctamente la información de un rol', function () {
    Permission::factory()->create(['name' => 'roles.view']);
    Permission::factory(5)->create();
    $allPermissionIds = Permission::all()->pluck('id');
    $this->role->permissions()->sync($allPermissionIds);

    $permissionsResponse = $this->getJson('/api/permissions');

    // Devuelve los permisos correctamente.
    $permissionsResponse->assertStatus(Response::HTTP_OK);

    $permissionsToAssign = collect($permissionsResponse->json('data'))->take(2)->pluck('id')->toArray();

    $payload = [
        'name'              => '      Rol nuevo del TEST    ',
        'description'       => 'descripcion del rol',
        'permissions_id'    => $permissionsToAssign
    ];

    $storeResponse = $this->postJson('/api/roles', $payload);


    $storeResponse->assertStatus(Response::HTTP_CREATED);   // Valida que se haya creado el rol


    $showResponse = $this->getJson("/api/roles/$storeResponse->json('data.id')");

    $showResponse->assertStatus(Response::HTTP_OK)
        ->assertJsonCount(2, 'data.permissions')
        ->assertJsonPath('data.name', $storeResponse->json('data.name'))
        ->assertJsonPath('data.permissions.0.id', $permissionsToAssign[0]['id']);
});

test('Falla al tratar de mostrar la información de un rol inexistente', function () {
    $permission = Permission::factory()->create(['name' => 'roles.view']);
    $this->role->permissions()->sync($permission);

    $reponse = $this->getJson("/api/roles/999");

    $reponse->assertStatus(Response::HTTP_BAD_REQUEST)
        ->assertJsonPath('success', false);
});

test('Falla al tratar de mostrar la información de un rol sin permisos', function () {
    $roleId = $this->role->id;

    $reponse = $this->getJson("/api/roles/{$roleId}");

    $reponse->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonPath('success', false);
});

// Destroy
test('Eliminar correctamente el registro de un rol');

test('Falla al tratar de eliminar un rol inexistente');

test('Falla al tratar de eliminar un rol sin permisos');

test('Falla al tratar de eliminar un rol que tiene usuarios asociados');