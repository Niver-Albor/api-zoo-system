<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreRoleRequest extends ApiFormRequest
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
            'name'          => 'required|string|max:255|unique:roles,name',
            'description'   => 'required|string',
            'permissions_id'    => 'required|array|min:1',
            'permissions_id.*'  => 'required|integer|exists:permissions,id',
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
