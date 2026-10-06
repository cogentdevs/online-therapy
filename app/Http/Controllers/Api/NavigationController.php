<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\FrontendSharedDataService;
use Illuminate\Http\JsonResponse;

class NavigationController extends Controller
{
    public function __construct(
        private readonly FrontendSharedDataService $frontendSharedDataService,
    ) {}

    public function index(): JsonResponse
    {
        $navigationCategories = $this->frontendSharedDataService->navigationCategories();
        $categoryData = fn (Category $category): array => [
            'id' => $category->id,
            'name' => $category->name,
            'language' => $category->language,
            'slug' => $category->navigation_slug,
            'web_url' => $category->navigation_url,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'navigation_categories' => $navigationCategories->map($categoryData)->values(),
                'footer_categories' => $navigationCategories->take(5)->map($categoryData)->values(),
            ],
        ]);
    }
}
