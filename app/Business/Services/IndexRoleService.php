<?php

namespace App\Business\Services;

use App\Business\DTOs\IndexRoleData;
use App\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexRoleService
{
    public function indexRole(IndexRoleData $filters): LengthAwarePaginator
    {
        $query = Role::query();

        if (!empty($filters->search))
            {
                $search = $filters->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
            }
        
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