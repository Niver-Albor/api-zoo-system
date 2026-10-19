<?php

namespace App\Business\Services;

use App\Business\DTOs\SaveMedicalRecordData;
use App\Models\Animal;
use App\Models\MedicalRecord;
use App\Traits\HasMedicalRecords;

class CreateMedicalRecordService
{
    use HasMedicalRecords;

    public function createMedicalRecord(SaveMedicalRecordData $dto): MedicalRecord
    {
        $this->validateDates($dto->performedAt, $dto->nextCheckupAt);

        $data = $dto->toArray();

        $animal = Animal::where('code', $data['animal_code'])->firstOrFail();
        $data['animal_id'] = $animal->id;
        unset($data['animal_code']);

        $data['uuid'] = uuid_create();

        $record = MedicalRecord::create($data)->load(['animal', 'user']);

        return $record;
    }
}