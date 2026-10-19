<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalRecordRequest extends ApiFormRequest
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
            'animal_code'       => 'required|string|exists:animals,code',
            'record_type'       => 'required|string|in:checkup,vaccination,deworming,emergency,correction',
            'diagnosis'         => 'required|string',
            'treatment'         => 'required|string',
            'performed_at'      => 'required|date|before_or_equal:now',
            'next_checkup_at'   => 'sometimes|nullable|date|after:performed_at'
        ];
    }
}
