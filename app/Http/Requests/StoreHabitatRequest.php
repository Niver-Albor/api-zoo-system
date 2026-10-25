<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreHabitatRequest extends ApiFormRequest
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
            'name'      => 'required|string|unique:habitats,name',
            'type'      => 'required|string|in:aviary,aquatic,terrestrial',
            'capacity'  => 'required|int|min:0',
            'status'    => 'required|string|in:available,maintenance',
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
