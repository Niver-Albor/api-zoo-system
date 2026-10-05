<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class FindAnimalRequest extends ApiFormRequest
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
            'code'  => 'required|string|max:100|exists:animals,code,deleted_at,NULL'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'code' => strtoupper(trim($this->route('code')))
        ]);
    }
}
