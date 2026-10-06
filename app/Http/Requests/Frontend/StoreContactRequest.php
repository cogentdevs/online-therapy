<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'g-recaptcha-response' => ['bail', 'required', 'string', 'captcha'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'نام درج کرنا ضروری ہے۔',
            'phone.required' => 'فون نمبر درج کرنا ضروری ہے۔',
            'email.required' => 'ای میل درج کرنا ضروری ہے۔',
            'email.email' => 'درست ای میل درج کریں۔',
            'subject.required' => 'موضوع درج کرنا ضروری ہے۔',
            'message.required' => 'پیغام درج کرنا ضروری ہے۔',
            'g-recaptcha-response.required' => 'براہ کرم reCAPTCHA مکمل کریں۔',
            'g-recaptcha-response.captcha' => 'reCAPTCHA کی تصدیق نہیں ہو سکی، دوبارہ کوشش کریں۔',
        ];
    }
}
