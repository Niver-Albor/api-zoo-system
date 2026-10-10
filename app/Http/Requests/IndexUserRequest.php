<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexUserRequest extends ApiFormRequest
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
            'status'            => 'nullable|string|in:active,inactive',
            'role'              => 'nullable|string|exists:roles,name',
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date|after_or_equal:start_date'
        ];
    }
}
