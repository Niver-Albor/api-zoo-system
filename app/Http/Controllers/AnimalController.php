<?php

namespace App\Http\Controllers;

use App\Business\DTOs\IndexAnimalData;
use App\Business\DTOs\SaveAnimalData;
use App\Business\Services\CreateAnimalService;
use App\Business\Services\DeleteAnimalService;
use App\Business\Services\IndexAnimalService;
use App\Business\Services\ShowAnimalService;
use App\Business\Services\UpdateAnimalService;
use App\Http\Requests\IndexAnimalRequest;
use App\Http\Requests\StoreAnimalRequest;
use App\Http\Requests\UpdateAnimalRequest;
use App\Http\Resources\AnimalResource;
use App\Models\Animal;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use function PHPUnit\Framework\isEmpty;

class AnimalController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected IndexAnimalService $indexAnimalService,
        protected ShowAnimalService $showAnimalService,
        protected CreateAnimalService $createAnimalService,
        protected UpdateAnimalService $updateAnimalService,
        protected DeleteAnimalService $deleteAnimalService,
    ){}

    public function index(IndexAnimalRequest $request)
    {
        $this->authorize('index', Animal::class);

        $filters = IndexAnimalData::fromRequest($request);

        $response = $this->indexAnimalService->indexAnimals($filters);

        return AnimalResource::collection($response);
    }

    public function show(Animal $animal)
    {
        $this->authorize('show', $animal);

        $response = $this->showAnimalService->showAnimal($animal);

        if ($response)
            {
                return $this->successResponse(
                    new AnimalResource($response),
                    'Animal encontrado exitosamente',
                    Response::HTTP_FOUND
                );
            }
        
        return $this->errorResponse();
    }

    public function store(StoreAnimalRequest $request)
    {
        $this->authorize('store', Animal::class);

        $dto = SaveAnimalData::fromRequest($request);

        $response =  $this->createAnimalService->createAnimal($dto);

        if ($response)
            {
                return $this->successResponse(
                    new AnimalResource($response),
                    'Animal creado correctamente',
                    Response::HTTP_CREATED
                );
            }
        
        return $this->errorResponse();
    }

    public function update(UpdateAnimalRequest $request, Animal $animal)
    {
        $this->authorize('update', $animal);

        $dto = SaveAnimalData::fromRequest($request);

        $response = $this->updateAnimalService->updateAnimal($dto, $animal);

        if ($response)
            {
                return $this->successResponse(
                    new AnimalResource($response),
                    'Animal actualizado correctamente'
                );
            }

        return $this->errorResponse();
    }

    public function destroy(Animal $animal)
    {
        $this->authorize('destroy', $animal);

        $response = $this->deleteAnimalService->deleteAnimal($animal);

        if ($response)
            {
                return $this->successResponse(
                    null,
                    'Animal eliminado correctamente'
                );
            }

        return $this->errorResponse();
    }
}
