<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSpeciesRequest extends ApiFormRequest
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
            'common_name'       => [
                'sometimes',
                'nullable',
                'string',
                Rule::unique('species', 'common_name')->ignore($this->route('species')),
            ],
            'scientific_name'   => [
                'sometimes',
                'nullable',
                'string',
                Rule::unique('species', 'scientific_name')->ignore($this->route('species')),
            ],
            'description'       => 'sometimes|nullable|string',
        ];
    }

    public function prepareForValidation()
    {
        if (isset($this->common_name))
            $this->merge([
                'common_name' => strtoupper(trim($this->common_name))
            ]);

        if (isset($this->scientific_name))
            $this->merge([
                'scientific_name' => strtoupper(trim($this->scientific_name))
            ]);
    }
}
