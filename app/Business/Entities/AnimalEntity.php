<?php

namespace App\Business\Entities;

use Carbon\Carbon;
use DomainException;

use function PHPUnit\Framework\isEmpty;

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
// 53:19
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

    public function validateData(array $data)
    {
        if (array_key_exists('birth_date', $data)) 
            $this->validateBirthDate($data['birth_date']);

        if (isset($data['admission_date']))
            $this->validateAdmissionDate($data['admission_date']);
        
        if (isset($data['habitat_id']))
            $this->validateHabitat($data['habitat_id']);
    }

    public function validateBirthDate(?string $birthDate = null, ?string $admissionDate = null)
    {
        if (!$birthDate && isEmpty($this->birthDate))
            return;
        
        $birthDate = Carbon::parse($birthDate);

        $admissionDate = ($admissionDate) 
            ? Carbon::parse($admissionDate) :Carbon::parse($this->admissionDate);
        
        if ($birthDate->isAfter($admissionDate)) 
            {
                throw new DomainException('La fecha de nacimiento no puede ser posterior a la fecha de admisión.');
            }
    }

    public function validateAdmissionDate(string $admissionDate, ?string $birthDate = null)
    {
        if (isEmpty($this->birthDate))
            return;
        
        $admissionDate = Carbon::parse($admissionDate);
        $birthDate = ($birthDate)
            ? Carbon::parse($birthDate) : Carbon::parse($this->birthDate);
        
        if ($admissionDate->isBefore($birthDate)) 
            {
                throw new DomainException('La fecha de admisión no puede ser previa a la fecha de nacimiento.');
            }
    }

    public function validateHabitat(int $habitatId)
    {
        return;
    }
}