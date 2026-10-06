<?php

namespace App\Http\Requests\Admin;

use App\Models\AuthorGeneralSetting;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAuthorRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'experience_detail' => ['nullable', 'string'],
            'experience_years' => ['nullable', 'integer', 'min:0'],
            'speciality' => ['nullable', 'string', 'max:255'],
            'picture' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'visibility' => ['nullable', 'array:'.implode(',', AuthorGeneralSetting::configurableFields())],
            'visibility.*' => ['boolean'],
        ];
    }
}
