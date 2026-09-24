<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;

/**
 * Base for every v1 API FormRequest: keeps 422s in the standardized envelope
 * (phase-3a-api-integration-plan.md §11) so controllers stay thin and every
 * error shape is identical.
 */
abstract class ApiFormRequest extends FormRequest
{
    /**
     * Persist the standardized VALIDATION_FAILED envelope.
     */
    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_FAILED',
                    'status' => 422,
                    'message' => 'The given data was invalid.',
                    'request_id' => (string) Str::uuid(),
                    'errors' => $validator->errors()->toArray(),
                ],
            ], 422)
        );
    }
}
