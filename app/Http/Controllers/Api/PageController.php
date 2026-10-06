<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PublicContentApiService;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    public function __construct(private readonly PublicContentApiService $publicContentApiService) {}

    public function show(string $slug): JsonResponse
    {
        $result = $this->publicContentApiService->legalPage($slug);
        $page = $result['page'];

        return response()->json(['success' => true, 'data' => ['page' => [
            'slug' => $slug, 'language' => $result['language'],
            'title' => $page?->title ?: $result['definition']['titles'][$result['language']],
            'content' => $page?->description, 'content_format' => 'trusted_html',
        ]]]);
    }
}
