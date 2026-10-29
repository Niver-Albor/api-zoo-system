<?php

namespace App\Business\Services;

use App\Business\DTOs\SaveAnimalData;
use App\Models\Animal;
use App\Traits\HasAnimals;
use Carbon\CarbonImmutable;

class UpdateAnimalService
{
    use HasAnimals;

    public function updateAnimal(SaveAnimalData $dto, Animal $animal): Animal
    {
        $admissionDate = $dto->admissionDate ?? CarbonImmutable::parse($animal->addmision_date);
        $birthDate = $dto->birthDate ?? CarbonImmutable::parse($animal->birth_date);

        $this->validateDates($admissionDate, $birthDate);

        if ($dto->habitatId)
            $this->validateHabitatCapacity($dto->habitatId);

        $data = $dto->toArray();

        $animal->update($data);

        $animal->load(['species', 'habitat']);

        return $animal;
    }
}