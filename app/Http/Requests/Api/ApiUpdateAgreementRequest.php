<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiUpdateAgreementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:200',
            'client_name' => 'sometimes|required|string|max:120',
            'client_email' => 'sometimes|required|email|max:150',
            'client_company' => 'nullable|string|max:150',
            'client_phone' => 'nullable|string|max:30',
            'client_address' => 'nullable|string|max:300',
            'contract_value' => 'sometimes|required|numeric|min:0|max:100000000',
            'currency' => 'sometimes|required|string|size:3',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'scope_of_work' => 'nullable|string|max:10000',
            'payment_terms' => 'nullable|string|max:5000',
            'content' => 'sometimes|required|string|max:50000',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'code' => 'VALIDATION_FAILED',
            'message' => 'The agreement update failed validation.',
            'errors' => $validator->errors(),
        ], 422));
    }
}

