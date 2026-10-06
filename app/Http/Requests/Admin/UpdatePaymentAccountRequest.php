<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdatePaymentAccountRequest extends FormRequest
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
            'bank_name' => ['required', 'string', 'max:255'],
            'account_title' => ['required', 'string', 'max:255'],
            'iban' => ['nullable', 'string', 'max:100'],
            'account_no' => ['required', 'string', 'max:100'],
            'branch_code' => ['nullable', 'string', 'max:100'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'bank_name' => Str::squish((string) $this->input('bank_name')),
            'account_title' => Str::squish((string) $this->input('account_title')),
            'iban' => $this->normalizedIdentifier('iban', true),
            'account_no' => $this->normalizedIdentifier('account_no'),
            'branch_code' => $this->normalizedIdentifier('branch_code', true),
        ]);
    }

    private function normalizedIdentifier(string $key, bool $uppercase = false): ?string
    {
        $value = $this->input($key);

        if (! is_string($value)) {
            return null;
        }

        $normalized = Str::of($value)->trim()->replaceMatches('/\s+/', '')->toString();

        if ($normalized === '') {
            return null;
        }

        return $uppercase ? Str::upper($normalized) : $normalized;
    }
}
