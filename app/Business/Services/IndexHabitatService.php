<?php

namespace App\Business\Services;

use App\Business\DTOs\IndexHabitatData;
use App\Models\Habitat;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexHabitatService
{
    public function indexHabitat(IndexHabitatData $filters): LengthAwarePaginator
    {
        $query = Habitat::query()->withCount('animals as animals_count');

        if (!empty($filters->search))
            {
                $search = $filters->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
            }

        if (!empty($filters->type))
            $query->where('type', $filters->type);

        if (!empty($filters->status))
            $query->where('status', $filters->status);

        if (!empty($filters->animalsCount))
            $query->having('animals_count', '>=', $filters->animalsCount);

        
        if (!empty($filters->splitFrom))
            $query->whereDate('created_at', '>=', $filters->splitFrom);

        if (!empty($filters->splitTo))
            $query->whereDate('created_at', '<=', $filters->splitTo);

        // Ordenamiento.
        $sortBy = $filters->sortBy ?? 'created_at';
        $sortDir = $filters->sortDirection ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        // Paginación.
        $perPage = $filters->perPage ?? 10;
        $page = $filters->page ?? 1;


        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}