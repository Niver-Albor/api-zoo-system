<?php

namespace App\Business\Services;

use App\Business\DTOs\IndexAnimalData;
use App\Models\Animal;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexAnimalService
{
    public function indexAnimals(IndexAnimalData $filters): LengthAwarePaginator
    {
        $query = Animal::with(['species', 'habitat'])
            ->select('animals.*')
            ->join('species', 'animals.species_id', '=', 'species.id')
            ->join('habitats', 'animals.habitat_id', '=', 'habitats.id');

        // Búsqueda general (por nombre o código del animal)
        if (!empty($filters->search)) {
            $query->where(function ($q) use ($filters) {
                $q->where('animals.name', 'like', "%{$filters->search}%")
                  ->orWhere('animals.code', 'like', "%{$filters->search}%");
            });
        }

        // Filtrado por estado de salud
        if (!empty($filters->healthStatus)) {
            $query->where('animals.health_status', $filters->healthStatus);
        }

        // Filtrado por especie
        if (!empty($filters->speciesId)) {
            $query->where('animals.species_id', $filters->speciesId);
        }

        // Filtrado por hábitat
        if (!empty($filters->habitatId)) {
            $query->where('animals.habitat_id', $filters->habitatId);
        }

        // Filtrado por sexo
        if (!empty($filters->sex)) {
            $query->where('animals.sex', $filters->sex);
        }

        // Ordenamiento
        $sortBy = $filters->sortBy ?? 'created_at';
        $sortDir = $filters->sortDirection ?? 'desc';
        $query->orderBy("animals.{$sortBy}", $sortDir);

        // Paginación
        $perPage = $filters->perPage ?? 10;
        $page = $filters->page ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}