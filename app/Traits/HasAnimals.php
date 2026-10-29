<?php

namespace App\Traits;

use App\Models\Habitat;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;

trait HasAnimals
{
    public function validateDates(CarbonImmutable $admissionDate, ?CarbonImmutable $birthDate)
    {
        if ($admissionDate->isAfter(now()))
            {
                throw ValidationException::withMessages([
                    'El registro no puede ser previo a la ejecución del procedimiento.'
                ]);
            }
        
        if ($birthDate && $birthDate->isAfter($admissionDate))
            {
                throw ValidationException::withMessages([
                        'La fecha de admisión no puede ser previa a la fecha de nacimiento.'
                    ]);
            }
    }

    public function validateHabitatCapacity(int | string $habitatId)
    {
        $habitat = Habitat::findOrFail($habitatId);

        $currentAnimalsCount = $habitat->animals()->count();

        if ($currentAnimalsCount >= $habitat->capacity)
            {
                throw ValidationException::withMessages([
                    "El habitat {$habitat->name} ha alcanzado su capacidad maxima de ejemplares."
                ]);
            }
    }
}
