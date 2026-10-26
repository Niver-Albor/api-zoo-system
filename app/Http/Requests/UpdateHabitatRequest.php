<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHabitatRequest extends ApiFormRequest
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
            'name'      => [
                'sometimes',
                'nullable',
                'string',
                Rule::unique('habitats', 'name')->ignore($this->route('habitat')),
            ],
            'type'      => 'sometimes|nullable|string|in:aviary,aquatic,terrestrial',
            'capacity'  => 'sometimes|nullable|integer|min:0',
             'status'   => 'sometimes|nullable|string|in:available,maintenance',
        ];
    }

    public function prepareForValidation()
    {
        if (isset($this->name))
            $this->merge([
                'name' => strtoupper(trim($this->name))
            ]);
    }
}
