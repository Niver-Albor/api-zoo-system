<?php

namespace App\Business\Services;

use App\Business\DTOs\IndexMedicalRecordData;
use App\Models\MedicalRecord;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexMedicalRecordService
{
    public function indexRecords(IndexMedicalRecordData $filters): LengthAwarePaginator
    {
        $query = MedicalRecord::with(['animal', 'user', 'replacesRecord'])
            ->select('medical_records.*')
            ->join('animals', 'medical_records.animal_id', '=', 'animals.id')
            ->join('users', 'medical_records.user_id', '=', 'users.id');

        // Filtrado por registro médicos.
        if(!empty($filters->search))
            $query->where('medical_records.uuid', $filters->search);

        if(!empty($filters->recordType))
            $query->where('medical_records.record_type', $filters->recordType);

        // Filtrado por animales.
        if(!empty($filters->animalCode))
            $query->where('animals.code', $filters->animalCode);

        // Filtrado por usuarios.
        if(!empty($filters->userId))
            $query->where('users.uuid', $filters->userId);

        // Filtrado por fechas.
        if(!empty($filters->createdFrom))
            $query->whereDate('medical_records.created_at', '>=', $filters->createdFrom);

        if(!empty($filters->createdTo))
            $query->whereDate('medical_records.created_at', '<=', $filters->createdTo);

        if(!empty($filters->performedFrom))
            $query->whereDate('medical_records.performed_at', '>=', $filters->performedFrom);

        if(!empty($filters->performedTo))
            $query->whereDate('medical_records.performed_at', '<=', $filters->performedTo);

        if(!empty($filters->nextCheckupFrom))
            $query->whereDate('medical_records.next_checkup_at', '>=', $filters->nextCheckupFrom);

        if(!empty($filters->nextCheckupTo))
            $query->whereDate('medical_records.next_checkup_at', '<=', $filters->nextCheckupTo);

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