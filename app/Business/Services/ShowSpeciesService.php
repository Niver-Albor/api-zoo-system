<?php

namespace App\Business\Services;

use App\Business\DTOs\BaseDTO;
use App\Models\Species;

class ShowSpeciesService
{
    public function showSpecies(Species $species): Species
    {
        $species->load(['animals'])->withCount(['animals as animals_count']);
        return $species;
    }
}