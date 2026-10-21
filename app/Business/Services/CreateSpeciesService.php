<?php

namespace App\Business\Services;

use App\Business\DTOs\BaseDTO;
use App\Models\Species;

class CreateSpeciesService
{
    public function createSpecies(BaseDTO $dto): Species
    {
        $data = $dto->toArray();
        $species = Species::create($data);

        return $species;
    }
}