<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAskQuestionRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'sawal' => ['required', 'string', 'max:2000'],
            'g-recaptcha-response' => ['bail', 'required', 'string', 'captcha'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'نام درج کریں۔',
            'email.required' => 'ای میل درج کریں۔',
            'email.email' => 'درست ای میل درج کریں۔',
            'phone.required' => 'فون نمبر درج کریں۔',
            'subject.required' => 'سوال کا عنوان درج کریں۔',
            'sawal.required' => 'اپنا سوال درج کریں۔',
            'sawal.max' => 'سوال 2000 حروف سے زیادہ نہیں ہو سکتا۔',
            'g-recaptcha-response.required' => 'براہ کرم reCAPTCHA مکمل کریں۔',
            'g-recaptcha-response.captcha' => 'reCAPTCHA کی تصدیق نہیں ہو سکی، دوبارہ کوشش کریں۔',
        ];
    }
}
