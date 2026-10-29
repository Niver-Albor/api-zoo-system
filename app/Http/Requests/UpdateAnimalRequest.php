<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class UpdateAnimalRequest extends ApiFormRequest
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
            'habitat_id'        => 'sometimes|integer|exists:habitats,id',
            'species_id'        => 'sometimes|integer|exists:species,id',
            'code'              => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
                Rule::unique('animals', 'code')->ignore($this->route('animal'))
            ],
            // 'code'              => 'sometimes|string|max:100|unique:animals,code,'. $this->route('animal') .',code',
            'name'              => 'sometimes|string|max:100',
            'sex'               => 'sometimes|in:male,female,unknown',
            'birth_date'        => 'nullable|sometimes|date|before_or_equal:today',
            'admission_date'    => 'sometimes|date|before_or_equal:today',
            'health_status'     => 'sometimes|in:healthy,treatment,quarantine',
        ];
    }

    protected function prepareForValidation()
    {
        if (isset($this->code))
            $this->merge([
                'code' => strtoupper(trim($this->code))
            ]);

        if (isset($this->name))
            $this->merge([
                'name' => strtoupper(trim($this->name))
            ]);
    }
}
