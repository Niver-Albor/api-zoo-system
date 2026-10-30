<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class IndexAnimalRequest extends ApiFormRequest
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
            'search'        => 'sometimes|nullable|string|max:100',
            'page'          => 'sometimes|nullable|integer|min:0',
            'per_page'      => 'sometimes|nullable|integer|min:1|max:100',
            'sort_by'       => 'sometimes|nullable|string|in:name,code,created_at,admission_date',
            'sort_direction'=> 'sometimes|nullable|string|in:asc,desc',
            'health_status' => 'sometimes|nullable|string||in:healthy,treatment,quarantine',
            'species_id'    => 'sometimes|nullable|integer|exists:species,id',
            'habitat_id'    => 'sometimes|nullable|integer|exists:habitats,id',
            'sex'           => 'sometimes|nullable|string|in:male,female,unknown',
        ];
    }
}