<?php

namespace App\Http\Requests\Admin;

use App\Models\FaqCategory;
use App\Models\Language;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFaqRequest extends FormRequest
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
            'faq_category_id' => [
                'required',
                'integer',
                Rule::exists((new FaqCategory)->getTable(), 'id')->where(fn ($query) => $query
                    ->where('language', $this->string('language')->toString())
                    ->where('isActive', true)),
            ],
            'question' => ['required', 'string'],
            'answer' => ['required', 'string', 'max:1000'],
            'isActive' => ['required', 'boolean'],
        ];
    }
}
