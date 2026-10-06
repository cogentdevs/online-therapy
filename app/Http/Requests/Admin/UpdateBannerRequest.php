<?php

namespace App\Http\Requests\Admin;

use App\Models\Banner;
use App\Models\Language;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBannerRequest extends FormRequest
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
        $banner = Banner::query()->find($this->route('id'));

        return [
            'language' => [
                'required',
                'string',
                Rule::exists((new Language)->getTable(), 'code'),
            ],
            'type' => ['required', 'string', Rule::in(['full', 'side-by-side'])],
            'position' => ['required', 'string', Rule::in(['right-top', 'left', 'center', 'bottom-full', 'category-detail-top-full'])],
            'image' => [
                'nullable',
                'image',
                'mimes:png,jpg,jpeg,webp',
                'max:5120',
            ],
            'image_2' => [
                Rule::requiredIf(fn (): bool => $this->string('type')->toString() === 'side-by-side' && blank($banner?->image_2)),
                'nullable',
                'image',
                'mimes:png,jpg,jpeg,webp',
                'max:5120',
            ],
            'isActive' => ['required', 'boolean'],
        ];
    }
}
