<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'subscription_product_id' => ['required', 'integer', 'exists:subscription_products,id'],
            'payment_method' => ['required', 'string', Rule::in(['easypaisa', 'jazzcash', 'card'])],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'subscription_product_id.required' => 'Please select a subscription product.',
            'subscription_product_id.integer' => 'The selected subscription product is invalid.',
            'subscription_product_id.exists' => 'The selected subscription product is invalid.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'The selected payment method is invalid.',
        ];
    }
}
