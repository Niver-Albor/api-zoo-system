<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\ApiFormRequest;

class UpdateUserRequest extends ApiFormRequest
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
        $isSelfUpdate = auth()->id() === $this->route('user')?->id;
        
        return [
            'name'              => 'sometimes|string',
            'old_password' => array_values(array_filter([
                $isSelfUpdate ? 'required_with:new_password' : null,
                'nullable', 'string',
                $isSelfUpdate ? 'current_password' : null,
            ])),
            'new_password'      => array_values(array_filter([
                'sometimes', 'string', 'min:8',
                $isSelfUpdate ? 'different:old_password' : null
            ])),
            'confirm_password'  => 'required_with:new_password|nullable|string|same:new_password',
            'role_name'         => [
                $isSelfUpdate ? 'prohibited' : 'sometimes',
                'string', 'exists:roles,name', 'not_in:super-admin,owner'
                ],
            'email' => [
                'sometimes', 'email',
                Rule::unique('users', 'email')->ignore($this->route('user'))
            ],
        ];
    }
}
