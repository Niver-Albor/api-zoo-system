<?php

namespace App\Business\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class SaveRoleData extends BaseDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $name,
        public readonly ?string $description,
        public readonly ?array $permissionsId,
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        $permissions = $request->validated('permissions_id');

        return new self(
            id: $request->validated('id') ?? null,
            name: $request->validated('name') ?? null,
            description: $request->validated('description') ?? null,
            permissionsId: $permissions ? array_map('intval', $permissions) : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name'              => $this->name,
            'description'       => $this->description,
            'permissions_id'    => $this->permissionsId,
        ]);
    }
}