<?php

namespace App\Http\Requests\Api\Account;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StartTwoFactorChallengeRequest extends FormRequest
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
            'action' => ['required', 'string', Rule::in(['enable', 'disable'])],
            'current_password' => ['nullable', 'required_if:action,disable', 'string', function (string $attribute, mixed $value, Closure $fail): void {
                if ($this->input('action') === 'disable' && (! is_string($value) || ! Hash::check($value, $this->user()->password))) {
                    $fail('The current password is incorrect.');
                }
            }],
        ];
    }
}
