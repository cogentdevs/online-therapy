<?php

namespace App\Http\Requests\Admin;

use App\Models\Consultancy;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConsultancyRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'button_label' => ['required', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'short_description' => ['nullable', 'string'],
            'description' => ['required', 'string'],
            'duration_type' => ['required', 'string', Rule::in(array_keys(Consultancy::DURATION_TYPES))],
            'duration_value' => ['required', 'integer', 'min:1'],
            'consultancy_medium' => ['required', 'string', Rule::in(array_keys(Consultancy::MEDIUMS))],
            'isActive' => ['required', 'boolean'],
            'isFeatured' => ['required', 'boolean'],
            'related_consultancy_ids' => ['nullable', 'array'],
            'related_consultancy_ids.*' => [
                'integer',
                'distinct',
                Rule::exists((new Consultancy)->getTable(), 'id'),
                Rule::notIn([(int) $this->route('id')]),
            ],
        ];
    }
}
