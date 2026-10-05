<?php

namespace App\Business\Services;

use App\Models\Animal;


class ShowAnimalService
{
    /**
     * Hola Mundo!
     */
    public function showAnimal(string $code)
    {
        $animal = Animal::where('code', 'LIKE', "{$code}%")->get();

        // dd($animal->toArray());

        return $animal->toArray();
    }
}