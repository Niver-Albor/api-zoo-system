<?php

namespace App\Business\Services;

use App\Business\Entities\AnimalEntity;
use App\Models\Animal;

class CreateAnimalService
{
    public function createAnimal(AnimalEntity $entity)
    {
        $animal = Animal::create($entity->toArray());

        $entity->id = $animal->id;

        return $entity->toArray();
    }
}