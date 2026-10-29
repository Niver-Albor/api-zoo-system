<?php

namespace App\Http\Controllers;

use App\Business\DTOs\IndexRoleData;
use App\Business\DTOs\SaveRoleData;
use App\Business\Services\IndexRoleService;
use App\Business\Services\StoreRoleService;
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
        protected StoreRoleService $storeRoleService,
        protected UpdateRoleService $updateRoleService,
        protected IndexRoleService $indexRoleService,
    ) {}

    public function store(StoreRoleRequest $request)
    {
        $this->authorize('store', Role::class);

        $dto = SaveRoleData::fromRequest($request);

        $response = $this->storeRoleService->storeRole($dto, $request->user());

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
}
