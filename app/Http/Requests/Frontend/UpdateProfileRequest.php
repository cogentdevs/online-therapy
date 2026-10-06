<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user('web')?->getKey())],
            'phone' => ['required', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_profile_image' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'نام درج کرنا ضروری ہے۔',
            'name.max' => 'نام 255 حروف سے زیادہ نہیں ہو سکتا۔',
            'email.required' => 'ای میل درج کرنا ضروری ہے۔',
            'email.email' => 'درست ای میل درج کریں۔',
            'email.unique' => 'یہ ای میل پہلے سے استعمال ہو رہی ہے۔',
            'phone.required' => 'فون نمبر درج کرنا ضروری ہے۔',
            'phone.max' => 'فون نمبر 255 حروف سے زیادہ نہیں ہو سکتا۔',
            'profile_image.image' => 'منتخب فائل تصویر ہونی چاہیے۔',
            'profile_image.mimes' => 'تصویر JPG، JPEG، PNG یا WEBP فارمیٹ میں ہونی چاہیے۔',
            'profile_image.max' => 'تصویر کا حجم 2 ایم بی سے زیادہ نہیں ہو سکتا۔',
        ];
    }
}
