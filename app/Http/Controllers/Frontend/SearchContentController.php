<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Magazine;
use App\Models\SearchContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchContentController extends Controller
{
    public function __invoke(Request $request, string $type, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'search' => ['required', 'string', 'max:100'],
        ]);
        $searchKeyword = Str::limit(trim($validated['search']), 100, '');

        abort_if($searchKeyword === '', 422);

        $content = match ($type) {
            'article' => Article::query()->findOrFail($id),
            'magazine' => Magazine::query()->findOrFail($id),
            default => abort(404),
        };

        SearchContent::query()->create([
            'content_type' => $content->getMorphClass(),
            'content_id' => $content->getKey(),
            'search_keyword' => $searchKeyword,
            'created_at' => now(),
        ]);

        return redirect()->to($this->detailUrl($content));
    }

    private function detailUrl(Model $content): string
    {
        $slug = Str::slug((string) $content->getAttribute('title'));

        return match (true) {
            $content instanceof Article => route('mazmoon-detail', [
                'id' => $content->getKey(),
                'slug' => $slug ?: 'article-'.$content->getKey(),
            ]),
            $content instanceof Magazine => route('shumara-detail', [
                'id' => $content->getKey(),
                'slug' => $slug ?: 'magazine-'.$content->getKey(),
            ]),
        };
    }
}
