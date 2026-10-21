<?php

namespace App\Business\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class SaveSpeciesData extends BaseDTO
{
    public function __construct(
        protected ?string $id,
        protected ?string $commonName,
        protected ?string $scientificName,
        protected ?string $description
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        return new self(
            id: $request->validated('id') ?? null,
            commonName: $request->validated('common_name') ?? null,
            scientificName: $request->validated('scientific_name') ?? null,
            description: $request->validated('description') ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'common_name'       => $this->commonName,
            'scientific_name'   => $this->scientificName,
            'description'       => $this->description
        ]);
    }
}