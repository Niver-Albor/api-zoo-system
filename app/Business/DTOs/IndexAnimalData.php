<?php

namespace App\Business\DTOs;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

class IndexAnimalData extends BaseDTO
{
    public function __construct(
        public readonly ?int $perPage,
        public readonly ?int $page,
        public readonly ?string $healthStatus,
        public readonly ?int $speciesId,
        public readonly ?int $habitatId,
        public readonly ?string $sex,
        public readonly ?string $search,
        public readonly ?string $sortBy,
        public readonly ?string $sortDirection,
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        return new self(
            perPage: (int) ($request->validated('per_page') ?? 10),
            page: (int) ($request->validated('page') ?? 0),
            healthStatus: $request->validated('health_status') ?? null,
            speciesId: $request->validated('species_id') ? 
                (int) $request->validated('species_id') : null,
            habitatId: $request->validated('habitat_id') ? 
                (int) $request->validated('habitat_id') : null,
            sex: $request->validated('sex') ?? null,
            search: $request->validated('search') ?? null,
            sortBy: $request->validated('sort_by') ?? null,
            sortDirection: $request->validated('sort_direction') ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'per_page'      => $this->perPage,
            'page'          => $this->page,
            'health_status' => $this->healthStatus,
            'species_id'    => $this->speciesId,
            'habitat_id'    => $this->habitatId,
            'sex'           => $this->sex,
            'search'        => $this->search,
            'sort_by'       => $this->sortBy,
            'sort_direction'=> $this->sortDirection,
        ]);
    }
}