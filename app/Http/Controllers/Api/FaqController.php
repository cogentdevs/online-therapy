<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FaqIndexRequest;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Services\PublicContentApiService;
use Illuminate\Http\JsonResponse;

class FaqController extends Controller
{
    public function __construct(private readonly PublicContentApiService $publicContentApiService) {}

    public function categories(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => [
            'categories' => $this->publicContentApiService->faqCategories()
                ->map(fn (FaqCategory $category): array => $this->categoryData($category))->values(),
        ]]);
    }

    public function index(FaqIndexRequest $request): JsonResponse
    {
        $result = $this->publicContentApiService->faqs($request->integer('category_id') ?: null);

        return response()->json(['success' => true, 'data' => [
            'category' => $result['category'] === null ? null : $this->categoryData($result['category']),
            'faqs' => $result['faqs']->map(fn (Faq $faq): array => [
                'id' => $faq->id, 'category_id' => $faq->faq_category_id,
                'question' => $faq->question, 'answer' => $faq->answer,
            ])->values(),
        ]]);
    }

    /** @return array<string, mixed> */
    private function categoryData(FaqCategory $category): array
    {
        return [
            'id' => $category->id, 'language' => $category->language, 'name' => $category->name,
            'icon_url' => filled($category->icon) && is_file(public_path($category->icon))
                ? asset(ltrim(str_replace('\\', '/', $category->icon), '/')) : null,
        ];
    }
}
