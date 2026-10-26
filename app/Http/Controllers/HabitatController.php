<?php

namespace App\Http\Controllers;

use App\Business\DTOs\IndexHabitatData;
use App\Business\DTOs\SaveHabitatData;
use App\Business\Services\CreateHabitatService;
use App\Business\Services\IndexHabitatService;
use App\Business\Services\UpdateHabitatService;
use App\Http\Requests\IndexHabitatRequest;
use App\Http\Requests\StoreHabitatRequest;
use App\Http\Requests\UpdateHabitatRequest;
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
        protected UpdateHabitatService $updateHabitatService,
        protected IndexHabitatService $indexHabitatService,
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

    public function update(UpdateHabitatRequest $request, Habitat $habitat)
    {
        $this->authorize('update', $habitat);

        $dto = SaveHabitatData::fromRequest($request);

        $response = $this->updateHabitatService->updateHabitat($habitat, $dto);

        if ($response)
            {
                return $this->successResponse(
                    new HabitatResource($response),
                    'Habitat actualizado correctamente',
                    Response::HTTP_OK
                );
            }
        
        return $this->errorResponse();
    }

    public function index(IndexHabitatRequest $request)
    {
        $this->authorize('view', Habitat::class);

        $filters = IndexHabitatData::fromRequest($request);

        $response = $this->indexHabitatService->indexHabitat($filters);


        return HabitatResource::collection($response);
    }
}
