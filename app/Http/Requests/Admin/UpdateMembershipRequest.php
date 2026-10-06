<?php

namespace App\Http\Requests\Admin;

use App\Models\Currency;
use App\Models\SubscriptionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMembershipRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'subscription_type_ids' => ['required', 'array', 'min:1'],
            'subscription_type_ids.*' => ['required', 'integer', 'distinct', Rule::exists((new SubscriptionType)->getTable(), 'id')->where('isActive', true)],
            'currency_id' => ['required', 'integer', Rule::exists((new Currency)->getTable(), 'id')->where('isActive', true)],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_value' => ['required', 'integer', 'min:1'],
            'duration_unit' => ['required', Rule::in(['day', 'month', 'year'])],
            'discount_type' => ['nullable', Rule::in(['fixed', 'percentage'])],
            'discount_value' => ['nullable', 'required_with:discount_type', 'numeric', 'min:0', Rule::when($this->input('discount_type') === 'percentage', ['max:100'])],
            'isActive' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('discount_type')) {
            $this->merge(['discount_type' => null, 'discount_value' => null]);
        }
    }
}
