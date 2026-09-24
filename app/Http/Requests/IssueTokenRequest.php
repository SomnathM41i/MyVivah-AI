<?php

namespace App\Http\Requests;

class IssueTokenRequest extends ApiFormRequest
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
     * Credential exchange: `client_id` = platform public_id (ULID), `client_secret`
     * = one of the platform's v4.local symmetric keys (base64url). Both are required;
     * the secret is verified against the encrypted-at-rest key material.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'string', 'max:26'],
            'client_secret' => ['required', 'string', 'max:512'],
            'scope' => ['sometimes', 'nullable', 'array'],
            'scope.*' => ['string', 'max:64'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'client_id.required' => 'A client_id is required.',
            'client_secret.required' => 'A client_secret is required.',
        ];
    }
}
