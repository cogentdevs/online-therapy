<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateSubscriptionNotificationSettingRequest extends FormRequest
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
            'first_reminder_days' => ['nullable', 'integer', 'min:0'],
            'second_reminder_days' => ['nullable', 'integer', 'min:0'],
            'third_reminder_days' => ['nullable', 'integer', 'min:0'],
            'isActive' => ['required', 'boolean'],
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $configuredDays = collect([
                    $this->input('first_reminder_days'),
                    $this->input('second_reminder_days'),
                    $this->input('third_reminder_days'),
                ])->filter(fn (mixed $value): bool => is_numeric($value) && (int) $value > 0)
                    ->map(fn (mixed $value): int => (int) $value);

                if ($configuredDays->duplicates()->isNotEmpty()) {
                    $validator->errors()->add('reminder_days', 'Positive reminder days must be unique.');
                }
            },
        ];
    }

    protected function prepareForValidation(): void
    {
        $normalized = [];

        foreach (['first_reminder_days', 'second_reminder_days', 'third_reminder_days'] as $field) {
            $value = $this->input($field);
            $normalized[$field] = blank($value) || (is_numeric($value) && (int) $value === 0)
                ? null
                : $value;
        }

        $this->merge($normalized);
    }
}
