<?php

namespace App\Business\Services;

use App\Business\DTOs\BaseDTO;
use App\Models\Role;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexUserService
{
    public function indexUser(array $filters): LengthAwarePaginator
    {
        $query = User::with('roles');

        if(!empty($filters['status']))
            $query->withTrashed();

        if(!empty($filters['role']))
            {
                $role = Role::where('name', $filters['role'])->firstOrFail();
                 $query->where('role_id', $role->id);
            }
        
        if(!empty($filters['search']))
            $query->where('name', 'LIKE', "%{$filters['search']}%");

        if (!empty($filters['start_date']))
            $query->whereDate('created_at', '>=', $filters['start_date']);

        if (!empty($filters['end_date']))
            $query->whereDate('created_at', '<=', $filters['end_date']);

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        $perPage = $filters['per_page'] ?? 10;
        $page = $filters['page'] ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $page);

    }
}