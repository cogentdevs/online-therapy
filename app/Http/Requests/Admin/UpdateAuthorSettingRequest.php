<?php

namespace App\Http\Requests\Admin;

use App\Models\AuthorGeneralSetting;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAuthorSettingRequest extends FormRequest
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
        $rules = [
            'visibility' => ['required', 'array:'.implode(',', AuthorGeneralSetting::configurableFields())],
        ];

        foreach (AuthorGeneralSetting::configurableFields() as $field) {
            $rules['visibility.'.$field] = ['required', 'boolean'];
        }

        return $rules;
    }
}
