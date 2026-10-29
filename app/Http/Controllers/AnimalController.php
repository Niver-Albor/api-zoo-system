<?php

namespace App\Http\Controllers;

use App\Business\Entities\AnimalEntity;
use App\Business\Services\CreateAnimalService;
use App\Business\Services\DestroyAnimalService;
use App\Business\Services\IndexAnimalService;
use App\Business\Services\ShowAnimalService;
use App\Business\Services\UpdateAnimalService;
use App\Http\Requests\FindAnimalRequest;
use App\Http\Requests\StoreAnimalRequest;
use App\Http\Requests\UpdateAnimalRequest;
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
        protected DestroyAnimalService $destroyAniamlService
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

    public function show(FindAnimalRequest $request)
    {
        $this->authorize('show', Animal::class);

        $payLoad = $this->showAnimalService->showAnimal($request->query('code'));

        // dd($payLoad);

        if ($payLoad)
            {
                return $this->successResponse(data: $payLoad, code: Response::HTTP_FOUND);
            }

        return $this->errorResponse(message:'Código invalido.', code:Response::HTTP_NOT_FOUND);
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

    public function update(UpdateAnimalRequest $request, string $code)
    {
        $this->authorize('update', Animal::class);

        $validatedData = $request->validated();

        $animal = $this->updateAnimalService->updateAnimal($validatedData, $code);

        return $this->successResponse(
            data: $animal,
            message: 'Animal actualizado correctamente',
            code: Response::HTTP_OK
        );
    }

    public function destroy(FindAnimalRequest $request)
    {
        $this->authorize('destroy', Animal::class);

        $data = $request->validated();

        $payLoad = $this->destroyAniamlService->destroyAnimal($data['code']);

        if (!$payLoad)
            {
                return $this->successResponse(
                    data: null,
                    message: 'Animal eliminado correctamente',
                    code: Response::HTTP_OK
                );
            }

        return $this->errorResponse(
            message: 'Código invalido',
            code: Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
