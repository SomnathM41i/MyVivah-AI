<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            // Honeypot anti-spam: real users never submit this field.
            'website' => ['sometimes', 'string', 'max:0'],
        ];
    }

    /**
     * Custom attribute names for validation messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'website' => 'website',
        ];
    }
}
