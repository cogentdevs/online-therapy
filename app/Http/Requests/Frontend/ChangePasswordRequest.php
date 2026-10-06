<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.current_password' => 'موجودہ پاس ورڈ درست نہیں ہے۔',
            'password.min' => 'نیا پاس ورڈ کم از کم 8 حروف پر مشتمل ہونا چاہیے۔',
            'password.confirmed' => 'نئے پاس ورڈ کی تصدیق مطابقت نہیں رکھتی۔',
            'password.different' => 'نیا پاس ورڈ موجودہ پاس ورڈ سے مختلف ہونا چاہیے۔',
        ];
    }
}
