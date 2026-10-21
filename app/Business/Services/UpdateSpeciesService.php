<?php

namespace App\Business\Services;

use App\Business\DTOs\BaseDTO;
use App\Models\Species;

class UpdateSpeciesService
{
    public function updateSpecies(Species $species, BaseDTO $dto): Species
    {
        $data = $dto->toArray();
        $species->updateOrFail($data);

        return $species;
    }
}