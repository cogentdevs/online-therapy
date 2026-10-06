<?php

namespace App\Http\Requests\Admin;

use App\Models\Language;
use App\Models\MetaTag;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMetaTagRequest extends FormRequest
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
            'title' => ['required', 'string', Rule::in(array_keys(MetaTag::CORE_PAGES))],
            'keywords' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('language') || $validator->errors()->has('title')) {
                    return;
                }

                $exists = MetaTag::query()
                    ->whereNull('table_name')
                    ->whereNull('table_id')
                    ->where('language', $this->string('language')->toString())
                    ->where('title', $this->string('title')->toString())
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('title', 'SEO for this page and language already exists.');
                }
            },
        ];
    }
}
