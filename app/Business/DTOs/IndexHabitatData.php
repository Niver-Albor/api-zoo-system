<?php

namespace App\Business\DTOs;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

class IndexHabitatData extends BaseDTO
{
    public function __construct(
        public readonly ?string $search,
        public readonly ?string $type,
        public readonly ?string $status,
        public readonly ?int $animalsCount,
        public readonly ?CarbonImmutable $splitFrom,
        public readonly ?CarbonImmutable $splitTo,
        public readonly ?int $page,
        public readonly ?int $perPage,
        public readonly ?string $sortBy,
        public readonly ?string $sortDirection,
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        return new self(
            search: $request->validated('search') ?? null,
            type: $request->validated('type') ?? null,
            status: $request->validated('status') ?? null,
            animalsCount: $request->validated('animals_count') ?? null,
            splitFrom: $request->validated('split_from') ?
                Carbon::parse($request->validated('split_from')) : null,
            splitTo: $request->validated('split_to') ?
                Carbon::parse($request->validated('split_to')) : null,
            page: $request->validated('page') ?? null,
            perPage: $request->validated('per_page') ?? null,
            sortBy: $request->validated('sort_by') ?? null,
            sortDirection: $request->validated('sort_direction') ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            //
        ]);
    }
}