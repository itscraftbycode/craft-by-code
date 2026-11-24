<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|max:150',
            'password' => 'required|string|min:8|max:255',
            'token_name' => 'nullable|string|max:100',
            'expires_in_days' => 'nullable|integer|min:1|max:90',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'code' => 'VALIDATION_FAILED',
            'message' => 'The provided login parameters failed validation.',
            'errors' => $validator->errors(),
        ], 422));
    }
}

