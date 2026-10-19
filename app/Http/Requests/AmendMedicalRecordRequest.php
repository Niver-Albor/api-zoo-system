<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AmendMedicalRecordRequest extends FormRequest
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
            'animal_code'       => 'prohibited',
            'record_type'       => 'required|string|in:checkup,vaccination,deworming,emergency,correction',
            'diagnosis'         => 'sometimes|string',
            'treatment'         => 'sometimes|string',
            'performed_at'      => 'sometimes|date|before_or_equal:now',
            'next_checkup_at'   => 'sometimes|nullable|date|after:performed_at'
        ];
    }
}
