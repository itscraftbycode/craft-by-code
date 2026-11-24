<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiCreateTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'abilities' => 'nullable|array',
            'abilities.*' => 'required|string|max:60',
            'expires_in_days' => 'nullable|integer|min:1|max:365',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'code' => 'VALIDATION_FAILED',
            'message' => 'The token creation parameters failed validation.',
            'errors' => $validator->errors(),
        ], 422));
    }
}

