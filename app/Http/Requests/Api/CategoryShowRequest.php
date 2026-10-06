<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryShowRequest extends FormRequest
{
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
            'type' => ['nullable', Rule::in(['articles', 'magazines'])],
            'search' => ['nullable', 'string', 'max:100'],
            'year' => ['nullable', 'integer', 'digits:4', 'between:1000,9999'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
