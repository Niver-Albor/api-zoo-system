<?php

namespace App\Http\Controllers;

use App\Business\Entities\AnimalEntity;
use App\Business\Services\CreateAnimalService;
use App\Http\Requests\StoreAnimalRequest;
use App\Http\Requests\UpdateAnimalRequest;
use App\Models\Animal;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AnimalController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected CreateAnimalService $createAnimalService
    ){}

    public function index(Request $request)
    {
        $perPage = $request->query("per_page", 10);
        $page = $request->query("page", 0);
        $offset = $page * $perPage;

        $allAnimals = Animal::
            when($request->query('health_status'), function ($query, $health_status) {
                $query->where('health_status', $health_status);
            })
            ->when($request->query('species_id'), function ($query, $species_id) {
                $query->where('species_id', $species_id);
            })
            ->when($request->query('habitat_id'), function ($query, $habitat_id) {
                $query->where('habitat_id', $habitat_id);
            })
            ->when($request->query('sex'), function ($query, $sex) {
                $query->where('sex', $sex);
            })
            ->when($request->query('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%");
                });
            })
            ->skip($offset)->take($perPage)->get();

        return $this->successResponse(data:$allAnimals,code:Response::HTTP_OK);
    }

    public function show(string $code)
    {
        $animal = Animal::where('code', 'LIKE', "{$code}%")->get();

        if ($animal->isEmpty())
            {
                return $this->errorResponse(message:'Código invalido.', code:Response::HTTP_NOT_FOUND);
            }
        return $this->successResponse(data: $animal, code: Response::HTTP_FOUND);
    }

    public function store(StoreAnimalRequest $request)
    {
        $animalEntity = AnimalEntity::createFromArray($request->validated());

        $animal = $this->createAnimalService->execute($animalEntity);

        return $this->successResponse(data: $animal, code: Response::HTTP_CREATED);
    }

    public function update(UpdateAnimalRequest $request, string $code)
    {
        $validatedData = $request->validated();
        $animal = Animal::where('code', $code)->firstOrFail();

        $animal->update($validatedData);
        $animal->refresh();

        return $this->successResponse(
            data: $animal,
            message: 'Animal actualizado correctamente',
            code: Response::HTTP_OK
        );
    }

    public function destroy(string $code)
    {
        $animal = Animal::where('code', $code)->firstOrFail();

        $animal->delete();
        $animal->refresh();

        return $this->successResponse(
            data: $animal,
            message: 'Animal eliminado correctamente',
            code: Response::HTTP_OK
        );
    }
}
