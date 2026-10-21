<?php

namespace App\Http\Controllers;

use App\Business\DTOs\SaveSpeciesData;
use App\Business\Services\CreateSpeciesService;
use App\Business\Services\UpdateSpeciesService;
use App\Http\Requests\StoreSpecieRequest;
use App\Http\Requests\UpdateSpeciesRequest;
use App\Http\Resources\SpeciesResource;
use App\Models\Species;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SpeciesController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected CreateSpeciesService $createSpeciesService,
        protected UpdateSpeciesService $updateSpeciesService,
    ) {}

    public function store(StoreSpecieRequest $request)
    {
        $this->authorize('create', Species::class);

        $dto = SaveSpeciesData::fromRequest($request);

        $response = $this->createSpeciesService->createSpecies($dto);

        if ($response)
            {
                return $this->successResponse(
                    new SpeciesResource($response),
                    'Especie registrada exitosamente',
                    Response::HTTP_CREATED
                );
            }

        return $this->errorResponse();
    }

    public function update(UpdateSpeciesRequest $request, Species $species)
    {
        $this->authorize('update', $species);

        $dto = SaveSpeciesData::fromRequest($request);

        $response = $this->updateSpeciesService->updateSpecies($species, $dto);

        if ($response)
            {
                return $this->successResponse(
                    new SpeciesResource($response),
                    'Especie actualizada exitosamente',
                );
            }
        
        return $this->errorResponse();
    }
}
