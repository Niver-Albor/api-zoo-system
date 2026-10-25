<?php

namespace App\Business\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class SaveHabitatData extends BaseDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $type,
        public readonly ?int $capacity,
        public readonly ?string $status,
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        return new self(
            name: $request->validated('name') ?? null,
            type: $request->validated('type') ?? null,
            capacity: $request->validated('capacity') ?? null,
            status: $request->validated('status') ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name'      => $this->name,
            'type'      => $this->type,
            'capacity'  => $this->capacity,
            'status'    => $this->status,
        ]);
    }
}