<?php

namespace App\Business\DTOs;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Http\FormRequest;

class IndexMedicalRecordData extends BaseDTO
{
    public function __construct(
        public ?string $search,
        public ?int $page,
        public ?int $perPage,
        public ?string $sortBy,
        public ?string $sortDirection,
        public ?string $animalCode,
        public ?string $userId,
        public ?string $recordType,
        public ?CarbonImmutable $createdFrom,
        public ?CarbonImmutable $createdTo,
        public ?CarbonImmutable $performedFrom,
        public ?CarbonInterface $performedTo,
        public ?CarbonImmutable $nextCheckupFrom,
        public ?CarbonImmutable $nextCheckupTo
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        return new self(
            search:             $request->validated('search') ?? null,
            page:               $request->validated('page') ?? null,
            perPage:            $request->validated('per_page') ?? null,
            sortBy:             $request->validated('sort_by') ?? null,
            sortDirection:      $request->validated('sort_direction') ?? null,
            animalCode:         $request->validated('animal_code') ?? null,
            userId:             $request->validated('user_id') ?? null,
            recordType:         $request->validated('record_type') ?? null,
            createdFrom:        $request->validated('created_from') ?
                Carbon::parse($request->validated('created_from')) : null,
            createdTo:          $request->validated('created_to') ? 
                Carbon::parse($request->validated('created_to')) : null,
            performedFrom:      $request->validated('performed_from') ? 
                Carbon::parse($request->validated('performed_from')) : null,
            performedTo:        $request->validated('performed_to') ? 
                Carbon::parse($request->validated('performed_to')) : null,
            nextCheckupFrom:    $request->validated('next_checkup_form') ? 
                Carbon::parse($request->validated('next_checkup_form')) : null,
            nextCheckupTo:      $request->validated('next_checkup_to') ?
                Carbon::parse($request->validated('next_checkup_to')) : null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            //
        ]);
    }
}