<?php

namespace App\Http\Requests\Admin;

use App\Models\Language;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBannerRequest extends FormRequest
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
            'language' => [
                'required',
                'string',
                Rule::exists((new Language)->getTable(), 'code'),
            ],
            'type' => ['required', 'string', Rule::in(['full', 'side-by-side'])],
            'position' => ['required', 'string', Rule::in(['right-top', 'left', 'center', 'bottom-full', 'category-detail-top-full'])],
            'image' => [
                'required',
                'image',
                'mimes:png,jpg,jpeg,webp',
                'max:5120',
            ],
            'image_2' => ['required_if:type,side-by-side', 'nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        ];
    }
}
