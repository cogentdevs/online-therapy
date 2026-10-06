<?php

namespace App\Http\Requests\Admin;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Language;
use App\Models\Magazine;
use App\Models\Tags;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
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
            'magazine_id' => ['nullable', 'integer', Rule::exists((new Magazine)->getTable(), 'id')],
            'publish_date' => ['nullable', 'date'],
            'short_description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'article' => ['required', 'string'],
            'category_id' => [
                'nullable', 'integer',
                Rule::exists((new Category)->getTable(), 'id')->where(fn ($query) => $query
                    ->where('isActive', true)->where('language', $this->input('language'))),
            ],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => [
                'integer', 'distinct',
                Rule::exists((new Tags)->getTable(), 'id')->where(fn ($query) => $query
                    ->where('isActive', true)->where('language', $this->input('language'))),
            ],
            'author_id' => [
                'nullable', 'integer',
                Rule::exists((new Author)->getTable(), 'id')->where(fn ($query) => $query->where('isActive', true)),
            ],
            'related_article_ids' => ['nullable', 'array'],
            'related_article_ids.*' => [
                'integer', 'distinct',
                Rule::exists((new Article)->getTable(), 'id'),
                Rule::notIn([(int) $this->route('id')]),
            ],
            'isFree' => ['required', 'boolean'],
            'free_until' => [
                Rule::excludeIf(! $this->boolean('isFree')),
                'nullable', 'date', 'after_or_equal:today',
            ],
            'isFeatured' => ['required', 'boolean'],
            'show_on_latest' => ['nullable', 'boolean'],
            'show_on_editorial_center' => ['nullable', 'boolean'],
            'show_on_editorial_featured' => ['nullable', 'boolean'],
            'show_visit_counter' => ['nullable', 'boolean'],
            'isActive' => ['required', 'boolean'],
            'keywords' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
        ];
    }
}
