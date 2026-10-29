<?php

namespace App\Http\Controllers;

use App\Business\DTOs\SaveAnimalData;
use App\Business\Entities\AnimalEntity;
use App\Business\Services\CreateAnimalService;
use App\Business\Services\DeleteAnimalService;
use App\Business\Services\IndexAnimalService;
use App\Business\Services\ShowAnimalService;
use App\Business\Services\UpdateAnimalService;
use App\Http\Requests\FindAnimalRequest;
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

    public function index(Request $request)
    {
        $this->authorize('index', Animal::class);

        $requestData = [
            'per_page'      => $request->query("per_page", 10),
            'page'          => $request->query("page", 0),
            'health_status' => $request->query("health_status", null),
            'species_id'    => $request->query('species_id', null),
            'habitat_id'    => $request->query('habitat_id', null),
            'sex'           => $request->query('sex', null),
            'search'        => $request->query('search', null)
        ];

        $requestData['offset'] = $requestData['per_page'] * $requestData['page'];

        $payLoad = $this->indexAnimalService->indexAnimals($requestData);

        return $this->successResponse(data:$payLoad,code:Response::HTTP_OK);
    }

    public function show(Animal $animal)
    {
        $this->authorize('show', $animal);

        $reponse = $this->showAnimalService->showAnimal($animal);

        if ($reponse)
            {
                return $this->successResponse(
                    new AnimalResource($reponse),
                    'Animal encontrado exitosamente',
                    Response::HTTP_FOUND
                );
            }
        
        return $this->errorResponse();
    }

    public function store(StoreAnimalRequest $request)
    {
        $this->authorize('store', Animal::class);

        $animalEntity = AnimalEntity::createFromArray($request->validated());

        $animal = $this->createAnimalService->createAnimal($animalEntity);

        return $this->successResponse(
            data: $animal, 
            message: 'Animal creado exitosamente.', 
            code: Response::HTTP_CREATED);
    }

    public function update(UpdateAnimalRequest $request, Animal $animal)
    {
        $this->authorize('update', $animal);

        $dto = SaveAnimalData::fromRequest($request);

        $reponse = $this->updateAnimalService->updateAnimal($dto, $animal);

        if ($reponse)
            {
                return $this->successResponse(
                    new AnimalResource($reponse),
                    'Animal actualizado correctamente'
                );
            }

        return $this->errorResponse();
    }

    public function destroy(Animal $animal)
    {
        $this->authorize('destroy', $animal);

        $reponse = $this->deleteAnimalService->deleteAnimal($animal);

        if ($reponse)
            {
                return $this->successResponse(
                    null,
                    'Animal eliminado correctamente'
                );
            }

        return $this->errorResponse();
    }
}
