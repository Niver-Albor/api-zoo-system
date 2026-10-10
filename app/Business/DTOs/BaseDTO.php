<?php

namespace App\Business\DTOs;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseDTO
{
    abstract public static function fromRequest(FormRequest $request): static;

    abstract public function toArray(): array;
}