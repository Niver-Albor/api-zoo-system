<?php

namespace App\Business\Services;

use App\Models\Habitat;
use DomainException;

class DeleteHabitatService
{
    public function deleteHabitat(Habitat $habitat): bool
    {
        if ($habitat->animals()->exists())
            {
                throw new DomainException('No se puede eliminar el habitat porque aún tiene animales asociados.');
            }
        return $habitat->delete();
    }
}