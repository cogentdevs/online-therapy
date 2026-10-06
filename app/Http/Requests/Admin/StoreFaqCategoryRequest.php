<?php

namespace App\Http\Requests\Admin;

use App\Models\FaqCategory;
use App\Models\Language;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFaqCategoryRequest extends FormRequest
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
            'language' => ['required', 'string', Rule::exists((new Language)->getTable(), 'code')],
            'name' => ['required', 'string', 'max:150', Rule::unique((new FaqCategory)->getTable(), 'name')->where(fn ($query) => $query->where('language', $this->string('language')->toString()))],
            'icon' => ['nullable', 'image', 'mimes:png,webp', 'max:2048'],
        ];
    }
}
