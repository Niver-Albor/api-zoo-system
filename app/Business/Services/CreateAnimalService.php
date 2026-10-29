<?php

namespace App\Business\Services;

use App\Business\DTOs\SaveAnimalData;
use App\Models\Animal;
use App\Traits\HasAnimals;
use Carbon\CarbonImmutable;

class CreateAnimalService
{
    use HasAnimals;

    public function createAnimal(SaveAnimalData $dto): Animal
    {
        $this->validateHabitatCapacity($dto->habitatId);

        $this->validateDates($dto->admissionDate, $dto->birthDate ?? null);

        $data = $dto->toArray();

        $animal = Animal::create($data);

        $animal->load(['species', 'habitat']);

        return $animal;
    }
}