<?php

namespace App\Http\Requests;

use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class ApiFormRequest extends FormRequest
{
    use ApiResponse;

    public function failedValidation(Validator $validator)
    {
        $response = $this->errorResponse(
            $validator->errors()->messages(), 
            Response::HTTP_UNPROCESSABLE_ENTITY
            );
        
        throw new HttpResponseException($response);
    }
}
