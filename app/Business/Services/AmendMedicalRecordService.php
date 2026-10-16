<?php

namespace App\Business\Services;

use App\Business\DTOs\SaveMedicalRecordData;
use App\Models\MedicalRecord;
use App\Traits\HasMedicalRecords;

class AmendMedicalRecordService
{
    use HasMedicalRecords;

    public function amendRecord(MedicalRecord $model, SaveMedicalRecordData $dto): MedicalRecord
    {
        $this->validateDates($dto->performedAt, $dto->nextCheckupAt);

        return $model;
    }
}