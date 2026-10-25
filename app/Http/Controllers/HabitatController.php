<?php

namespace App\Http\Controllers;

use App\Business\DTOs\SaveHabitatData;
use App\Business\Services\CreateHabitatService;
use App\Http\Requests\StoreHabitatRequest;
use App\Http\Resources\HabitatResource;
use App\Models\Habitat;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HabitatController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected CreateHabitatService $createHabitatService,
    ) {}

    public function store(StoreHabitatRequest $request)
    {
        $this->authorize('store', Habitat::class);

        $dto = SaveHabitatData::fromRequest($request);

        $response = $this->createHabitatService->createHabitat($dto);

        if ($response)
            {
                return $this->successResponse(
                    new HabitatResource($response),
                    'Habitat creado correctamente',
                    Response::HTTP_CREATED
                );
            }
        
        return $this->errorResponse();
    }
}
