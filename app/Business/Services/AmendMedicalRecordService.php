<?php

namespace App\Business\Services;

use App\Business\DTOs\SaveMedicalRecordData;
use App\Models\Animal;
use App\Models\MedicalRecord;
use App\Traits\HasMedicalRecords;
use Carbon\CarbonImmutable;

class AmendMedicalRecordService
{
    use HasMedicalRecords;

    public function amendRecord(MedicalRecord $oldRecord, SaveMedicalRecordData $dto): MedicalRecord
    {
        $baseData = $oldRecord->only([
            'diagnosis',
            'treatment',
            'performed_at',
            'next_checkup_at'
        ]);

        $mergedData = array_merge($baseData, $dto->toArray());

        $this->validateDates(
            CarbonImmutable::createFromTimeString($mergedData['performed_at']), 
            CarbonImmutable::createFromTimeString($mergedData['next_checkup_at'])
            );

        $mergedData['animal_id'] = $oldRecord->animal_id;
        $mergedData['replaces_id'] = $oldRecord->id;
        $mergedData['uuid'] = uuid_create();

        $record = MedicalRecord::create($mergedData)->load(['animal', 'user', 'replacesRecord']);

        return $record;
    }
}