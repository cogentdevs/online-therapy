<?php

namespace App\Http\Requests\Admin;

use App\Models\HomeSection;
use App\Models\Language;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHomeSectionRequest extends FormRequest
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
            'position' => ['required', 'string', Rule::in(array_keys(HomeSection::POSITIONS))],
            'section_condition' => ['required', 'integer', Rule::in(array_keys(HomeSection::SECTION_LABELS))],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => [Rule::requiredIf(fn (): bool => in_array($this->integer('section_condition'), [1, 3, 4, 5], true)), 'nullable', 'string'],
            'title_2' => ['nullable', 'string', 'max:255'],
            'description_2' => [Rule::requiredIf(fn (): bool => $this->integer('section_condition') === 5), 'nullable', 'string'],
            'image' => [Rule::requiredIf(fn (): bool => in_array($this->integer('section_condition'), [2, 3, 4, 6], true)), 'nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'image_2' => [Rule::requiredIf(fn (): bool => $this->integer('section_condition') === 6), 'nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
