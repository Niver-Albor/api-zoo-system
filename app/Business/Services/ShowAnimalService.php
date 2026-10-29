<?php

namespace App\Business\Services;

use App\Models\Animal;

class ShowAnimalService
{
    public function showAnimal(Animal $animal): Animal
    {
        $animal->load(['species', 'habitat']);

        return $animal;
    }
}