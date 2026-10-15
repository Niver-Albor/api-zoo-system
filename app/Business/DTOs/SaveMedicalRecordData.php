<?php

namespace App\Business\DTOs;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

class SaveMedicalRecordData extends BaseDTO
{
    public function __construct(
        public readonly string $animalCode,
        public readonly int $userId,
        public readonly ?int $replacesId,
        public readonly string $recordType,
        public readonly string $diagnosis,
        public readonly string $treatment,
        public readonly CarbonImmutable $performedAt,
        public readonly ?CarbonImmutable $nextCheckupAt
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        return new self(
            animalCode: $request->validated('animal_code'),
            userId: $request->user()->id,
            replacesId: $request->validated('replaces_id') ?? null,
            recordType: $request->validated('record_type'),
            diagnosis: $request->validated('diagnosis'),
            treatment: $request->validated('treatment'),
            performedAt: CarbonImmutable::parse($request->validated('performed_at')),
            nextCheckupAt: CarbonImmutable::parse($request->validated('next_checkup_at')) ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'animal_code'       => $this->animalCode,
            'user_id'           => $this->userId,
            'replaces_id'       => $this->replacesId,
            'record_type'       => $this->recordType,
            'diagnosis'         => $this->diagnosis,
            'treatment'         => $this->treatment,
            'performed_at'      => $this->performedAt,
            'next_checkup_at'   => $this->nextCheckupAt
        ]);
    }
}