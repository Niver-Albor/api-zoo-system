<?php

namespace App\Business\Services;

use App\Models\Animal;
use App\Business\Entities\AnimalEntity;

class UpdateAnimalService
{
    public function execute(Array $data, string $code)
    {
        $animal = Animal::where('code', $code)->firstOrFail();

        $entity = AnimalEntity::createFromArray($animal->toArray());

        $entity->validateData($data);

        $animal->update($data);

        $animal->refresh();

        return $animal->toArray();
    }
}