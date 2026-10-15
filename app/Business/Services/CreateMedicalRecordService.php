<?php

namespace App\Business\Services;

use App\Business\DTOs\BaseDTO;
use App\Business\DTOs\SaveMedicalRecordData;
use App\Models\Animal;
use App\Models\MedicalRecord;
use Carbon\CarbonImmutable;
use InvalidArgumentException;

class CreateMedicalRecordService
{
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


    protected function validateDates(CarbonImmutable $performedAt, ?CarbonImmutable $nextCheckupAt)
    {
        if ($performedAt->isAfter(now()))
            throw new InvalidArgumentException('El registro no puede ser previo a la ejecución del procedimiento.');
    
        if ($nextCheckupAt && $nextCheckupAt->lessThanOrEqualTo($performedAt))
            throw new InvalidArgumentException('La próxima fecha no puede ser previa o igual a la del procedimiento.');
    }
}