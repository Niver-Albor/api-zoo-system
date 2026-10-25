<?php

namespace App\Business\Services;

use App\Business\DTOs\SaveHabitatData;
use App\Models\Habitat;

class CreateHabitatService
{
    public function createHabitat(SaveHabitatData $dto): Habitat
    {
        $data = $dto->toArray();
        $habitat = Habitat::create($data);

        return $habitat;
    }
}