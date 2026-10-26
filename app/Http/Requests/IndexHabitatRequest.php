<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexHabitatRequest extends ApiFormRequest
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
            'search'            => 'sometimes|nullable|string',
            'type'              => 'sometimes|nullable|string|in:aviary,aquatic,terrestrial',
            'status'            => 'sometimes|nullable|string|in:available,maintenance',
            'animals_count'     => 'sometimes|nullable|integer|min:0',
            'slipt_from'        => 'sometimes|nullable|date',
            'split_to'          => 'sometimes|nullable|date',
            'page'              => 'nullable|integer',
            'per_page'          => 'nullable|integer',
            'sort_by'           => 'nullable|string|in:name,type,status',
            'sort_direction'    => 'nullable|string|in:asc,desc',
        ];
    }

    public function prepareForValidation()
    {
        if (isset($this->search))
            $this->merge([
                'search' => strtoupper(trim($this->search))
            ]);
    }
}
