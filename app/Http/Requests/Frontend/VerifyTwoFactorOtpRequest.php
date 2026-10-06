<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class VerifyTwoFactorOtpRequest extends FormRequest
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
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'otp' => ['required', 'digits:6'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['otp.required' => 'تصدیقی کوڈ درج کریں۔', 'otp.digits' => 'تصدیقی کوڈ ٹھیک 6 ہندسوں کا ہونا چاہیے۔'];
    }
}
