<?php

namespace App\Business\Services;

use App\Models\Animal;

class DeleteAnimalService
{
    public function deleteAnimal(Animal $animal): bool
    {

        return $animal->delete();
    }
}