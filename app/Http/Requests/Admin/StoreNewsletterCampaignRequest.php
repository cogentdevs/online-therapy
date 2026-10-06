<?php

namespace App\Http\Requests\Admin;

use App\Models\Article;
use App\Models\Magazine;
use App\Models\NewsletterCampaignContent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNewsletterCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['title' => ['required', 'string', 'max:255'], 'short_description' => ['nullable', 'string', 'max:2000'], 'contents' => ['required', 'array', 'min:1'], 'contents.*.type' => ['required', Rule::in(NewsletterCampaignContent::TYPES)], 'contents.*.id' => ['required', 'integer'], 'contents.*.sort_order' => ['required', 'integer', 'min:1', 'distinct']];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $seen = [];
            foreach ((array) $this->input('contents', []) as $i => $item) {
                $type = $item['type'] ?? '';
                $id = (int) ($item['id'] ?? 0);
                $key = $type.':'.$id;
                if (isset($seen[$key])) {
                    $validator->errors()->add("contents.$i.id", 'Duplicate content is not allowed.');
                } $seen[$key] = true;
                $model = $type === 'article' ? Article::class : ($type === 'magazine' ? Magazine::class : null);
                if ($model && ! $model::query()->whereKey($id)->where('isActive', true)->where('status', 'published')->exists()) {
                    $validator->errors()->add("contents.$i.id", 'Selected content is unavailable.');
                }
            }
        });
    }
}
