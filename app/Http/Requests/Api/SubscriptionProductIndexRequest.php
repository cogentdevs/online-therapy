<?php

namespace App\Http\Requests\Api;

use App\Models\SubscriptionProduct;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionProductIndexRequest extends FormRequest
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
            'type' => ['required', 'string', Rule::in([
                SubscriptionProduct::FOR_PLAN,
                SubscriptionProduct::FOR_MEMBERSHIP,
            ])],
        ];
    }
}
