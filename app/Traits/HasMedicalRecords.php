<?php

namespace App\Traits;

use Carbon\CarbonImmutable;
use InvalidArgumentException;

trait HasMedicalRecords
{
    public function validateDates(CarbonImmutable $performedAt, ?CarbonImmutable $nextCheckupAt)
    {
        if ($performedAt->isAfter(now()))
            throw new InvalidArgumentException('El registro no puede ser previo a la ejecución del procedimiento.');
    
        if ($nextCheckupAt && $nextCheckupAt->lessThanOrEqualTo($performedAt))
            throw new InvalidArgumentException('La próxima fecha no puede ser previa o igual a la del procedimiento.');
    }
}
