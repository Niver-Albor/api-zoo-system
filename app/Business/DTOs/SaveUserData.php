<?php

namespace App\Business\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class SaveUserData extends BaseDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $password = null,
        public readonly ?string $role_name = null,
    ) {}

    public static function fromRequest(FormRequest $request): self
    {
        
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $request->validated('password') ?? $request->validated('new_password'),
            role_name: $request->validated('role_name')
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ]);
    }
}