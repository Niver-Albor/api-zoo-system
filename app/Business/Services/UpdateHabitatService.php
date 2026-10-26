<?php

namespace App\Business\Services;

use App\Business\DTOs\BaseDTO;
use App\Business\DTOs\SaveHabitatData;
use App\Models\Habitat;
use DomainException;

class UpdateHabitatService
{
    public function updateHabitat(Habitat $habitat, SaveHabitatData $dto): Habitat
    {
        if(isset($dto->capacity))
            $this->validateHabitatCapcity($habitat, $dto->capacity);

        $data = $dto->toArray();
        $habitat->updateOrFail($data);

        return $habitat;
    }

    protected function validateHabitatCapcity(Habitat $habitat, int $newCapacity)
    {
        $currentAnimalsCount = $habitat->animals()->count();

        if ($currentAnimalsCount > $newCapacity)
            {
                throw new DomainException(
                    "No se puede reducir la capacidad a {$newCapacity} porque el hábitat ya tiene {$currentAnimalsCount} animales asignados."
                );
            }
    }
}