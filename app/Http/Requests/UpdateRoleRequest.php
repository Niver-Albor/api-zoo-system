<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class UpdateRoleRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($this->route('role')),
            ],
            'description'   => 'sometimes|nullable|string',
            'permissions_id' => 'sometimes|array|min:1',
            'permissions_id.*'  => 'sometimes|integer|exists:permissions,id',
        ];
    }

    #[Override]
    public function prepareForValidation()
    {
        if (isset($this->name))
            $this->merge([
                'name'  => strtoupper(trim($this->name))
            ]);
    }
}
