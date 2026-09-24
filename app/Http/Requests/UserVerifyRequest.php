<?php

namespace App\Http\Requests;

class UserVerifyRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is fully handled by the route middleware
     * (ValidatePlatformToken + EnsurePlatformAccess:realtime_chat:write); the
     * platform context is derived from the token, never the body.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'external_user_id' => ['required', 'string', 'max:255'],
            'local_public_id' => ['nullable', 'string', 'size:26'],
        ];
    }
}
