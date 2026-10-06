<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVideoRequest extends FormRequest
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
            'thumbnail' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'video_link' => ['required', 'url', 'max:2048'],
            'short_description' => ['nullable', 'string'],
            'is_free' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'is_share' => ['required', 'boolean'],
        ];
    }
}
