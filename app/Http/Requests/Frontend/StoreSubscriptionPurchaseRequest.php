<?php

namespace App\Http\Requests\Frontend;

use App\Models\PaymentAccount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreSubscriptionPurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'payment_account_id' => [
                'required',
                'integer',
                Rule::exists((new PaymentAccount)->getTable(), 'id')
                    ->where(fn ($query) => $query->where('is_active', true)),
            ],
            'payment_slip' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'mimetypes:image/jpeg,image/png,image/webp,application/pdf',
                'max:5120',
            ],
            'transaction_id' => ['nullable', 'string', 'max:100'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'payment_account_id.required' => 'براہ کرم ادائیگی کا اکاؤنٹ منتخب کریں۔',
            'payment_account_id.exists' => 'منتخب کردہ ادائیگی اکاؤنٹ دستیاب نہیں ہے۔',
            'payment_slip.required' => 'براہ کرم ادائیگی کی رسید اپ لوڈ کریں۔',
            'payment_slip.file' => 'ادائیگی کی رسید درست فائل ہونی چاہیے۔',
            'payment_slip.mimes' => 'رسید JPG، JPEG، PNG، WEBP یا PDF فارمیٹ میں ہونی چاہیے۔',
            'payment_slip.mimetypes' => 'رسید کی فائل کا فارمیٹ درست نہیں ہے۔',
            'payment_slip.max' => 'رسید کی فائل 5 ایم بی سے زیادہ نہیں ہونی چاہیے۔',
            'transaction_id.max' => 'ٹرانزیکشن یا حوالہ نمبر 100 حروف سے زیادہ نہیں ہو سکتا۔',
        ];
    }

    protected function prepareForValidation(): void
    {
        $transactionId = $this->input('transaction_id');

        if (is_string($transactionId)) {
            $normalizedTransactionId = Str::of($transactionId)->trim()->toString();
            $this->merge([
                'transaction_id' => $normalizedTransactionId !== '' ? $normalizedTransactionId : null,
            ]);
        }
    }
}
