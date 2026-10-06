<?php

namespace App\Http\Requests\Admin;

use App\Models\Article;
use App\Models\TazaShumaraArticle;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTazaShumaraArticleRequest extends FormRequest
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
            'article_id' => [
                'required', 'integer', Rule::exists((new Article)->getTable(), 'id'),
                Rule::unique((new TazaShumaraArticle)->getTable(), 'article_id')
                    ->where('taza_shumara_id', (int) $this->route('tazaShumara')),
            ],
            'display_width' => ['required', Rule::in(array_keys(TazaShumaraArticle::DISPLAY_WIDTHS))],
            'position' => ['required', Rule::in(array_keys(TazaShumaraArticle::POSITIONS))],
            'sort_order' => ['required', 'integer', 'min:0'],
        ];
    }
}
