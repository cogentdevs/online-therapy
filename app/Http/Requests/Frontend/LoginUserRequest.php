<?php

namespace App\Http\Requests\Frontend;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginUserRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
            'login_source' => ['sometimes', 'string', 'in:homepage'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'email.required' => 'براہ کرم اپنی ای میل درج کریں۔',
            'email.email' => 'براہ کرم درست ای میل درج کریں۔',
            'password.required' => 'براہ کرم اپنا پاس ورڈ درج کریں۔',
            'password.string' => 'پاس ورڈ درست نہیں ہے۔',
            'remember.boolean' => 'مجھے یاد رکھیں کا انتخاب درست نہیں ہے۔',
        ];
    }

    public function authenticate(): User
    {
        $key = $this->throttleKey();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'بہت زیادہ کوششیں ہو چکی ہیں۔ براہ کرم '.RateLimiter::availableIn($key).' سیکنڈ بعد دوبارہ کوشش کریں۔',
            ]);
        }

        RateLimiter::hit($key, 60);
        $provider = Auth::guard('web')->getProvider();
        $credentials = $this->safe()->only(['email', 'password']);
        $user = $provider->retrieveByCredentials($credentials);
        $hasValidCredentials = $user instanceof User && $provider->validateCredentials($user, $credentials);
        $isFrontendUser = $hasValidCredentials && $user->hasRole('user', 'web');
        $inactive = $isFrontendUser && ! $user->is_active;

        if (! $isFrontendUser || $inactive) {
            throw ValidationException::withMessages([
                'email' => $inactive
                    ? 'آپ کا اکاؤنٹ ابھی ایکٹیویٹ نہیں ہوا۔ براہ کرم اپنے ای میل میں موجود ایکٹیویشن لنک استعمال کریں۔'
                    : 'ای میل یا پاس ورڈ درست نہیں ہے۔',
            ]);
        }

        RateLimiter::clear($key);

        return $user;
    }

    public function throttleKey(): string
    {
        return 'front-login:'.hash('sha256', Str::lower($this->string('email')->trim()->toString()).'|'.$this->ip());
    }
}
