<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreAnimalRequest extends ApiFormRequest
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
            'habitat_id'        => 'required|integer|exists:habitats,id',
            'species_id'        => 'required|integer|exists:species,id',
            'code'              => 'required|string|max:100|unique:animals,code',
            'name'              => 'required|string|max:100',
            'sex'               => 'nullable|in:male,female,unknown',
            'birth_date'        => 'nullable|date|before_or_equal:today',
            'admission_date'    => 'required|date|before_or_equal:today',
            'health_status'     => 'nullable|in:healthy,treatment,quarantine',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
        'code' => $this->code ? strtoupper(trim($this->code)) : null,
        ]);

        $this->merge([
        'name' => $this->name ? strtoupper(trim($this->name)) : null,
        ]);
    }
}
