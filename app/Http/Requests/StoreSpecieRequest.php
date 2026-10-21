<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreSpecieRequest extends ApiFormRequest
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
            'common_name'       => 'required|string|unique:species,common_name',
            'scientific_name'   => 'required|string|unique:species,scientific_name',
            'description'       => 'required|string'
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
