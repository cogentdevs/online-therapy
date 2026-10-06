<?php

namespace App\Http\Requests\Admin;

use App\Models\Article;
use App\Models\Language;
use App\Models\Magazine;
use App\Models\TazaShumaraArticle;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTazaShumaraRequest extends FormRequest
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
            'magazine_id' => ['required', 'integer', Rule::exists((new Magazine)->getTable(), 'id')],
            'cover_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'show_title' => ['required', 'boolean'],
            'show_short_description' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'articles' => ['nullable', 'array'],
            'articles.*.article_id' => ['required', 'integer', 'distinct', Rule::exists((new Article)->getTable(), 'id')],
            'articles.*.display_width' => ['required', Rule::in(array_keys(TazaShumaraArticle::DISPLAY_WIDTHS))],
            'articles.*.position' => ['required', Rule::in(array_keys(TazaShumaraArticle::POSITIONS))],
            'articles.*.sort_order' => ['required', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['articles' => $this->filledArticleRows()]);
    }

    /** @return array<int, array<string, mixed>> */
    private function filledArticleRows(): array
    {
        return collect($this->input('articles', []))
            ->filter(fn (mixed $row): bool => is_array($row) && ! (
                blank($row['article_id'] ?? null)
                && ($row['display_width'] ?? 'full') === 'full'
                && ($row['position'] ?? 'top') === 'top'
                && (string) ($row['sort_order'] ?? '0') === '0'
            ))
            ->values()
            ->all();
    }
}
