<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexMedicalRecordRequest extends ApiFormRequest
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
            'search'            => 'nullable|string|max:100',
            'page'              => 'nullable|integer',
            'per_page'          => 'nullable|integer',
            'sort_by'           => 'nullable|string|in:name,created_at',
            'sort_direction'    => 'nullable|string|in:asc,desc',
            'animal_code'       => 'nullable|string|max:100|exists:animals,code',
            'user_id'           => 'nullable|string|max:100|exists:users,uuid',
            'record_type'       => 'nullable|string',
            'created_from'      => 'nullable|date',
            'created_to'        => 'nullable|date',
            'performed_from'    => 'nullable|date',
            'performed_to'      => 'nullable|date',
            'next_checkup_form' => 'nullable|date',
            'next_checkup_to'   => 'nullable|date',
        ];
    }
}
