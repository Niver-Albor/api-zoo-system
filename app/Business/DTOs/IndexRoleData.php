<?php

namespace App\Business\DTOs;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

class IndexRoleData extends BaseDTO
{
    public function __construct(
        public readonly ?string $search,
        public readonly ?CarbonImmutable $splitFrom,
        public readonly ?CarbonImmutable $splitTo,
        public readonly ?int $page,
        public readonly ?int $perPage,
        public readonly ?string $sortby,
        public readonly ?string $sortDirection,
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        return new self(
            search: $request->validated('search') ?? null,
            splitFrom: $request->validated('split_from') ?
                CarbonImmutable::parse($request->validated('split_from')) : null,
            splitTo: $request->validated('split_to') ?
                CarbonImmutable::parse($request->validated('split_to')) : null,
            page: $request->validated('page') ?? null,
            perPage: $request->validated('per_page') ?? null,
            sortby: $request->validated('sort_by') ?? null,
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