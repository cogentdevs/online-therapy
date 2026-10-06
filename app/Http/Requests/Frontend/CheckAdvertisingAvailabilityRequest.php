<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckAdvertisingAvailabilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user('web') !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from_date' => ['bail', 'required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'to_date' => ['bail', 'required', 'date_format:Y-m-d', 'after_or_equal:from_date'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'from_date.required' => 'آغاز کی تاریخ منتخب کریں۔',
            'from_date.date_format' => 'آغاز کی درست تاریخ منتخب کریں۔',
            'from_date.after_or_equal' => 'آغاز کی تاریخ آج سے پہلے نہیں ہو سکتی۔',
            'to_date.required' => 'اختتام کی تاریخ منتخب کریں۔',
            'to_date.date_format' => 'اختتام کی درست تاریخ منتخب کریں۔',
            'to_date.after_or_equal' => 'اختتام کی تاریخ آغاز کی تاریخ سے پہلے نہیں ہو سکتی۔',
        ];
    }
}
