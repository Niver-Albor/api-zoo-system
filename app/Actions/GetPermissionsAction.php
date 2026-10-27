<?php

namespace App\Actions;

use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Gate;

class GetPermissionsAction
{
    use ApiResponse;

    public function __invoke()
    {

        Gate::authorize('view', Permission::class);

        $permissions = Permission::all();

        return PermissionResource::collection($permissions);
    }
}