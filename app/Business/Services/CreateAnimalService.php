<?php

namespace App\Business\Services;

use App\Business\Entities\AnimalEntity;
use App\Models\Animal;

class CreateAnimalService
{
    public function execute(AnimalEntity $animalEntity)
    {
        $animal = Animal::create($animalEntity->toArray());

        $animalEntity->id = $animal->id;

        return $animalEntity->toArray();
    }
}