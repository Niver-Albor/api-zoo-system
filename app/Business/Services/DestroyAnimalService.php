<?php

namespace App\Business\Services;

use App\Models\Animal;
use DomainException;

class DestroyAnimalService
{
    public function destroyAnimal(string $code)
    {
        if (empty($code))
            throw new DomainException('Código no proporsionado');

        $animal = Animal::where('code', $code)->firstOrFail();

        $animal->delete();
        $animal = Animal::where('code', $code)->get()->toArray();
        
        return $animal;
    }
}