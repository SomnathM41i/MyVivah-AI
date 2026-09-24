<?php

namespace App\Http\Requests;

/**
 * Single-key revocation request (Phase 3C).
 *
 * `key_fingerprint` is the same 40-char SHA-256 prefix exposed as the `kid` in
 * the keys list and in PASETO footers — no raw secret is ever accepted here.
 */
class KeyRevokeRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'key_fingerprint' => ['required', 'string', 'size:40'],
        ];
    }
}
