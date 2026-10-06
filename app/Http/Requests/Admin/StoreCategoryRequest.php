<?php

namespace App\Http\Requests\Admin;

use App\Models\Language;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreCategoryRequest extends FormRequest
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
            'entries' => ['required', 'array', 'min:1'],
            'entries.*.name' => ['nullable', 'string', 'max:255'],
            'entries.*.image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'entries.*.keywords' => ['nullable', 'string'],
            'entries.*.meta_description' => ['nullable', 'string'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $entries = $this->input('entries', []);

                if (! is_array($entries)) {
                    return;
                }

                foreach (array_keys($entries) as $index) {
                    $name = $this->input("entries.$index.name");

                    if ((is_string($name) && trim($name) !== '') || $this->hasFile("entries.$index.image")) {
                        return;
                    }
                }

                $validator->errors()->add('entries', 'Enter a category name or select an image.');
            },
        ];
    }
}
