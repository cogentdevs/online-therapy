<?php

namespace App\Http\Requests\Admin;

use App\Models\Author;
use App\Models\Category;
use App\Models\Language;
use App\Models\Magazine;
use App\Models\StorageProvider;
use App\Models\Tags;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMagazineRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'issue_number' => ['nullable', 'string', 'max:255'],
            'publish_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => [
                'integer', 'distinct',
                Rule::exists((new Category)->getTable(), 'id')->where(fn ($query) => $query
                    ->where('isActive', true)->where('language', $this->input('language'))),
            ],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => [
                'integer', 'distinct',
                Rule::exists((new Tags)->getTable(), 'id')->where(fn ($query) => $query
                    ->where('isActive', true)->where('language', $this->input('language'))),
            ],
            'author_ids' => ['nullable', 'array'],
            'author_ids.*' => [
                'integer', 'distinct',
                Rule::exists((new Author)->getTable(), 'id')->where(fn ($query) => $query->where('isActive', true)),
            ],
            'related_magazine_ids' => ['nullable', 'array'],
            'related_magazine_ids.*' => [
                'integer', 'distinct',
                Rule::exists((new Magazine)->getTable(), 'id'),
                Rule::notIn([(int) $this->route('id')]),
            ],
            'pdf' => ['nullable', 'file', 'mimes:pdf', 'max:204800'],
            'provider_ids' => ['nullable', 'array'],
            'provider_ids.*' => [
                'integer', 'distinct',
                Rule::exists((new StorageProvider)->getTable(), 'id')
                    ->where(fn ($query) => $query->where('is_active', true)),
            ],
            'isFree' => ['required', 'boolean'],
            'free_until' => [
                Rule::excludeIf(! $this->boolean('isFree')),
                'nullable', 'date', 'after_or_equal:today',
            ],
            'isFeatured' => ['required', 'boolean'],
            'show_visit_counter' => ['nullable', 'boolean'],
            'is_downloadable' => ['nullable', 'boolean'],
            'isActive' => ['required', 'boolean'],
            'keywords' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
        ];
    }
}
