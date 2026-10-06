<?php

namespace App\Http\Requests\Admin;

use App\Models\Currency;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateCurrencyRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'code' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique((new Currency)->getTable(), 'code')->ignore((int) $this->route('id')),
            ],
            'symbol' => ['nullable', 'string', 'max:20'],
            'isActive' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $code = $this->input('code');

        if (is_string($code)) {
            $normalizedCode = Str::upper(trim($code));
            $this->merge(['code' => $normalizedCode !== '' ? $normalizedCode : null]);
        }
    }
}
