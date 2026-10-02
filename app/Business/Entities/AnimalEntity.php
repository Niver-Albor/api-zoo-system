<?php

namespace App\Business\Entities;

class AnimalEntity 
{
    public function __construct(
        public ?int $id,
        public int $habitatId,
        public int $speciesId,
        public string $code,
        public string $name,
        public ?string $sex,
        public ?string $birthDate,
        public ?string $admissionDate,
        public ?string $healthStatus,
    ){}

    public static function createFromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            habitatId: $data['habitat_id'],
            speciesId: $data['species_id'],
            code: strtoupper(trim($data['code'])),
            name: $data['name'],
            sex: $data['sex'] ?? 'unknown',
            birthDate: $data['birth_date'] ?? null,
            admissionDate: $data['admission_date'] ?? date('Y-m-d'),
            healthStatus: $data['health_status'] ?? 'healthy',
        );
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'habitat_id'     => $this->habitatId,
            'species_id'     => $this->speciesId,
            'code'           => $this->code,
            'name'           => $this->name,
            'sex'            => $this->sex,
            'birth_date'     => $this->birthDate,
            'admission_date' => $this->admissionDate,
            'health_status'  => $this->healthStatus,
        ];
    }
}