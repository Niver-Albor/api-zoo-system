<?php

namespace App\Business\Services;

use App\Business\DTOs\BaseDTO;
use App\Models\MedicalRecord;

class ShowMedicalRecordService
{
    public function showRecord(MedicalRecord $record): MedicalRecord
    {
        
        return $record->load(['animal', 'user', 'replacedBy', 'replacesRecord']);
    }
}