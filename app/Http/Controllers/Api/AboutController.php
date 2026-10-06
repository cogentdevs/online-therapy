<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Services\PublicContentApiService;
use Illuminate\Http\JsonResponse;

class AboutController extends Controller
{
    public function __construct(private readonly PublicContentApiService $publicContentApiService) {}

    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => [
            'sections' => $this->publicContentApiService->aboutSections()->map(fn (About $section): array => [
                'id' => $section->id, 'language' => $section->language,
                'section_condition' => $section->section_condition, 'title' => $section->title,
                'content_1' => $section->description, 'title_2' => $section->title_2,
                'content_2' => $section->description_2,
                'image_1_url' => $this->availableAssetUrl($section->image),
                'image_2_url' => $this->availableAssetUrl($section->image_2),
            ])->values(),
        ]]);
    }

    private function availableAssetUrl(?string $path): ?string
    {
        return filled($path) && is_file(public_path($path))
            ? asset(ltrim(str_replace('\\', '/', $path), '/')) : null;
    }
}
