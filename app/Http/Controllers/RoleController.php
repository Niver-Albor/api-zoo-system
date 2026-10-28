<?php

namespace App\Http\Controllers;

use App\Business\DTOs\SaveRoleData;
use App\Business\Services\StoreRoleService;
use App\Http\Requests\StoreRoleRequest;
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
}
