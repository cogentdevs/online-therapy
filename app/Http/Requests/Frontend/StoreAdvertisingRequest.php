<?php

namespace App\Http\Requests\Frontend;

use App\Services\AdPlacementAvailabilityService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Validator;

class StoreAdvertisingRequest extends CheckAdvertisingAvailabilityRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string', 'max:10000'],
            'placements' => ['required', 'array', 'min:1'],
            'placements.*' => ['required', 'string'],
            'g-recaptcha-response' => ['bail', 'required', 'string', 'captcha'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $placements = $this->input('placements');

            if (! is_array($placements)) {
                return;
            }

            $availability = app(AdPlacementAvailabilityService::class);
            $seen = [];

            foreach ($placements as $placement) {
                if (! is_string($placement)) {
                    continue;
                }

                if (isset($seen[$placement])) {
                    $validator->errors()->add('placements', 'ایک تشہیری مقام صرف ایک بار منتخب کریں۔');

                    return;
                }

                $seen[$placement] = true;

                [$pageName, $place] = array_pad(explode(':', $placement, 2), 2, '');

                if (! $availability->isValidPlacement($pageName, $place)) {
                    $validator->errors()->add('placements', 'درست تشہیری مقام منتخب کریں۔');

                    return;
                }
            }
        }];
    }

    /** @return array<int, array{page_name: string, place: string}> */
    public function selectedPlacements(): array
    {
        return array_map(static function (string $placement): array {
            [$pageName, $place] = explode(':', $placement, 2);

            return ['page_name' => $pageName, 'place' => $place];
        }, $this->validated('placements'));
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            ...parent::messages(),
            'name.required' => 'نام درج کریں۔',
            'email.required' => 'ای میل درج کریں۔',
            'email.email' => 'درست ای میل درج کریں۔',
            'phone.required' => 'فون نمبر درج کریں۔',
            'company.required' => 'کمپنی یا برانڈ کا نام درج کریں۔',
            'placements.required' => 'کم از کم ایک تشہیری مقام منتخب کریں۔',
            'placements.min' => 'کم از کم ایک تشہیری مقام منتخب کریں۔',
            'g-recaptcha-response.required' => 'براہ کرم reCAPTCHA مکمل کریں۔',
            'g-recaptcha-response.captcha' => 'reCAPTCHA کی تصدیق نہیں ہو سکی، دوبارہ کوشش کریں۔',
        ];
    }
}
