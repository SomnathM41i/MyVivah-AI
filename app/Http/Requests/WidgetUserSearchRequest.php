<?php

namespace App\Http\Requests;

class WidgetUserSearchRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['required', 'string', 'min:2', 'max:100'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:20'],
            'cursor' => ['sometimes', 'nullable', 'string', 'max:512'],
        ];
    }
}
