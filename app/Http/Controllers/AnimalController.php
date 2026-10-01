<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnimalRequest;
use App\Models\Animal;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AnimalController extends Controller
{
    use ApiResponse;

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
        $animal = Animal::find($code);

        if(empty($animal))
            {
                return $this->errorResponse(message:'Código invalido.', code:Response::HTTP_NOT_FOUND);
            }
        return $this->successResponse($animal);
    }

    public function store(StoreAnimalRequest $request)
    {
        $validatedData = $request->validated();

        $animal = Animal::create($validatedData);
        $animal->refresh();

        return $this->successResponse(data: $animal, code: Response::HTTP_CREATED);
    }
}
