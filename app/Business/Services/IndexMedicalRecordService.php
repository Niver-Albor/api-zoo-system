<?php

namespace App\Business\Services;

use App\Business\DTOs\BaseDTO;
use App\Models\MedicalRecord;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexMedicalRecordService
{
    public function indexRecords(array $filters): LengthAwarePaginator
    {
        $query = MedicalRecord::with(['animal', 'user', 'replacesRecord'])
            ->select('medical_records.*')
            ->join('animals', 'medical_records.animal_id', '=', 'animals.id')
            ->join('users', 'medical_records.user_id', '=', 'users.id');

        // Filtrado por registro médicos.
        if(!empty($filters['search']))
            $query->where('medical_records.uuid', $filters['search']);

        if(!empty($filters['record_type']))
            $query->where('medical_records.record_type', $filters['record_type']);

        // Filtrado por animales.
        if(!empty($filters['animal_code']))
            $query->where('animals.code', $filters['animal_code']);

        // Filtrado por usuarios.
        if(!empty($filters['user_id']))
            $query->where('users.uuid', $filters['user_id']);

        // Filtrado por fechas.
        if(!empty($filters['created_from']))
            $query->whereDate('medical_records.created_at', '>=', $filters['created_from']);

        if(!empty($filters['created_to']))
            $query->whereDate('medical_records.created_at', '<=', $filters['created_to']);

        if(!empty($filters['performed_from']))
            $query->whereDate('medical_records.performed_at', '>=', $filters['performed_from']);

        if(!empty($filters['performed_to']))
            $query->whereDate('medical_records.performed_at', '<=', $filters['performed_to']);

        if(!empty($filters['next_checkup_from']))
            $query->whereDate('medical_records.next_checkup_at', '>=', $filters['next_checkup_from']);

        if(!empty($filters['next_checkup_to']))
            $query->whereDate('medical_records.next_checkup_at', '<=', $filters['next_checkup_to']);

        // Ordenamiento.
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        // Paginación.
        $perPage = $filters['per_page'] ?? 10;
        $page = $filters['page'] ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}