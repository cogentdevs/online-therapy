<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnsubscribeNewsletterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'reason' => ['required', Rule::in(['too_many', 'not_relevant', 'not_now', 'other'])],
            'other_reason' => ['nullable', 'required_if:reason,other', 'string', 'max:500'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'reason.required' => 'براہ کرم ان سبسکرائب کرنے کی وجہ منتخب کریں۔',
            'reason.in' => 'منتخب کردہ وجہ درست نہیں ہے۔',
            'other_reason.required_if' => 'براہ کرم مختصر وجہ درج کریں۔',
            'other_reason.max' => 'وجہ 500 حروف سے زیادہ نہیں ہو سکتی۔',
        ];
    }
}
