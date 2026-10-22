<?php

namespace App\Business\Services;

use App\Business\DTOs\BaseDTO;
use App\Models\Species;
use DomainException;

class DeleteSpeciesService
{
    public function deleteSpecies(Species $species): bool
    {
        if ($species->animals()->exists())
            {
                throw new DomainException('No se puede eliminar la especie porque aún tiene animales asociados.');
            }
        
        return $species->delete();
    }
}