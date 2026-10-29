<?php

namespace App\Business\DTOs;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

class SaveAnimalData extends BaseDTO
{
    public function __construct(
        public readonly ?int $habitatId,
        public readonly ?int $speciesId,
        public readonly ?string $code,
        public readonly ?string $name,
        public readonly ?string $sex,
        public readonly ?CarbonImmutable $birthDate,
        public readonly ?CarbonImmutable $admissionDate,
        public readonly ?string $healthStatus,
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        return new self(
            habitatId: $request->validated('habitat_id') ?? null,
            speciesId: $request->validated('species_id') ?? null,
            code: $request->validated('code') ?? null,
            name: $request->validated('name') ?? null,
            sex: $request->validated('sex') ?? null,
            birthDate: $request->validated('birth_date') ?
            CarbonImmutable::parse($request->validated('birth_date')) : null,
            admissionDate: $request->validated('admission_date') ?
            CarbonImmutable::parse($request->validated('admission_date')) : null,
            healthStatus: $request->validated('health_status') ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'habitat_id'        => $this->habitatId,
            'species_id'        => $this->speciesId,
            'code'              => $this->code,
            'name'              => $this->name,
            'sex'               => $this->sex,
            'birth_date'        => $this->birthDate,
            'admission_date'    => $this->admissionDate,
            'health_status'     => $this->healthStatus,
        ]);
    }
}