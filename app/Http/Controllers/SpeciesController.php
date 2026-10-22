<?php

namespace App\Http\Controllers;

use App\Business\DTOs\IndexSpeciesData;
use App\Business\DTOs\SaveSpeciesData;
use App\Business\Services\CreateSpeciesService;
use App\Business\Services\IndexSpeciesService;
use App\Business\Services\ShowSpeciesService;
use App\Business\Services\UpdateSpeciesService;
use App\Http\Requests\IndexSpeciesRequest;
use App\Http\Requests\StoreSpecieRequest;
use App\Http\Requests\UpdateSpeciesRequest;
use App\Http\Resources\SpeciesResource;
use App\Models\Species;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SpeciesController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected CreateSpeciesService $createSpeciesService,
        protected UpdateSpeciesService $updateSpeciesService,
        protected IndexSpeciesService $indexSpeciesService,
        protected ShowSpeciesService $showSpeciesService,
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

    public function index(IndexSpeciesRequest $request)
    {
        $this->authorize('view', Species::class);

        $filters = IndexSpeciesData::fromRequest($request);

        $response = $this->indexSpeciesService->indexSpecies($filters);

        return SpeciesResource::collection($response);
    }

    public function show(FormRequest $request, Species $species)
    {
        $this->authorize('view', Species::class);

        $response = $this->showSpeciesService->showSpecies($species);

        if ($response)
            {
                return $this->successResponse(
                    new SpeciesResource($response),
                );
            }
        
        return $this->errorResponse();
    }
}
