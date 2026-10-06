<?php

namespace App\Http\Requests\Admin;

use App\Models\HomeSection;
use App\Models\Language;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHomeSectionRequest extends FormRequest
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
        $homeSection = HomeSection::query()->find($this->route('id'));
        $condition = $this->integer('section_condition');

        return [
            'language' => [
                'required',
                'string',
                Rule::exists((new Language)->getTable(), 'code'),
            ],
            'position' => ['required', 'string', Rule::in(array_keys(HomeSection::POSITIONS))],
            'section_condition' => ['required', 'integer', Rule::in(array_keys(HomeSection::SECTION_LABELS))],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => [Rule::requiredIf(fn (): bool => in_array($condition, [1, 3, 4, 5], true)), 'nullable', 'string'],
            'title_2' => ['nullable', 'string', 'max:255'],
            'description_2' => [Rule::requiredIf(fn (): bool => $condition === 5), 'nullable', 'string'],
            'image' => [Rule::requiredIf(fn (): bool => in_array($condition, [2, 3, 4, 6], true) && blank($homeSection?->image)), 'nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'image_2' => [Rule::requiredIf(fn (): bool => $condition === 6 && blank($homeSection?->image_2)), 'nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
