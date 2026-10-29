<?php

namespace App\Http\Controllers;

use App\Business\DTOs\IndexRoleData;
use App\Business\DTOs\SaveRoleData;
use App\Business\Services\CreateRoleService;
use App\Business\Services\DeleteRoleService;
use App\Business\Services\IndexRoleService;
use App\Business\Services\ShowRoleService;
use App\Business\Services\UpdateRoleService;
use App\Http\Requests\IndexRoleRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected CreateRoleService $createRoleService,
        protected UpdateRoleService $updateRoleService,
        protected IndexRoleService $indexRoleService,
        protected ShowRoleService $showRoleService,
        protected DeleteRoleService $deleteRoleService,
    ) {}

    public function store(StoreRoleRequest $request)
    {
        $this->authorize('store', Role::class);

        $dto = SaveRoleData::fromRequest($request);

        $response = $this->createRoleService->createRole($dto, $request->user());

        if ($response)
            {
                return $this->successResponse(
                    new RoleResource($response),
                    'rol creado correctamente',
                    Response::HTTP_CREATED
                );
            }
        
        return $this->errorResponse();
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $this->authorize('update', $role);

        $dto = SaveRoleData::fromRequest($request);

        $response = $this->updateRoleService->updateRole($role, $dto, $request->user());

        
        if ($response)
            {
                return $this->successResponse(
                    new RoleResource($response),
                    'rol actualizado correctamente',
                );
            }

        return $this->errorResponse();
    }

    public function index(IndexRoleRequest $request)
    {
        $this->authorize('view', Role::class);

        $filters = IndexRoleData::fromRequest($request);

        $response = $this->indexRoleService->indexRole($filters);

        return RoleResource::collection($response);
    }

    public function show(Role $role)
    {
        $this->authorize('show', $role);

        $response = $this->showRoleService->showRole($role);

        if ($response)
            {
                return $this->successResponse(
                    new RoleResource($response),
                );
            }

        return $this->errorResponse();
    }

    public function destroy(Role $role)
    {
        $this->authorize('destroy', $role);

        $response = $this->deleteRoleService->deleteRole($role);

        if ($response)
            {
                return $this->successResponse(
                    null,
                    'Rol eliminado correctamente'
                );
            }
        
        return $this->errorResponse();
    }
}
