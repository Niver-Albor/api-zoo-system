<?php

namespace App\Business\Services;

use App\Models\Habitat;

class ShowHabitatService
{
    public function showHabitat(Habitat $habitat): Habitat
    {
        $habitat->load(['animals'])->withCount(['animals as animals_count']);

        return $habitat;
    }
}